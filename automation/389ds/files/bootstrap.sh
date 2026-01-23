#!/usr/bin/env bash
#
# config script to idempotently bootstrap 389 ds based on https://www.port389.org/docs/389ds/howto/howto-deploy-389ds-on-openshift.html
#
set -euof pipefail

# constants
BOOTSTRAP_RM_NAME="rmanager"
LDAP_PORT=3389
BIND_DN="cn=Directory Manager"

replica_id=0
for ha_peer_host_outer in ${BOOTSTRAP_HA_PEER_HOSTS}; do
  replica_id=$((replica_id+1))

  echo "== Directory Server bootstrap =="
  echo "HA Peer       : ${ha_peer_host_outer}"
  echo "Replica ID    : ${replica_id}"
  echo "Backend         ${BOOTSTRAP_BACKEND_NAME}"

  DSCONF_PARAMS=(
    "ldap://${ha_peer_host_outer}:${LDAP_PORT}"
    -D "${BIND_DN}"
    -w "${DS_DM_PASSWORD}"
  )

  echo

  echo "Waiting for Directory Server to be ready..."
  until dsconf "${DSCONF_PARAMS[@]}" monitor server >/dev/null 2>&1; do
    sleep 5
  done
  echo "Directory Server is up."

  echo "Disallowing anonymous access..."
  dsconf "${DSCONF_PARAMS[@]}" config replace nsslapd-allow-anonymous-access=off

  echo "Ensuring backend exists..."
  if ! dsconf "${DSCONF_PARAMS[@]}" backend suffix get "${DS_SUFFIX_NAME}" >/dev/null 2>&1; then
    dsconf "${DSCONF_PARAMS[@]}" backend create \
      --suffix "${DS_SUFFIX_NAME}" \
      --be-name "${BOOTSTRAP_BACKEND_NAME}" \
      --create-suffix
  else
    echo "Backend already exists."
  fi

  echo "Ensuring replication is enabled..."
  if ! dsconf "${DSCONF_PARAMS[@]}" replication get --suffix "${DS_SUFFIX_NAME}" >/dev/null 2>&1; then
    dsconf "${DSCONF_PARAMS[@]}" replication enable \
      --suffix "${DS_SUFFIX_NAME}" \
      --role=supplier \
      --replica-id "${replica_id}"
  else
    echo "Replication already enabled."
  fi

  echo "Ensuring replication manager exists..."
  dsconf "${DSCONF_PARAMS[@]}" replication create-manager \
    --name "${BOOTSTRAP_RM_NAME}" \
    --passwd "${BOOTSTRAP_RM_PASSWORD}" \
    --suffix "${DS_SUFFIX_NAME}"

  echo "Ensuring replication agreements exist..."

  for ha_peer_host_inner in ${BOOTSTRAP_HA_PEER_HOSTS}; do

    if [[ "${ha_peer_host_inner}" == "${ha_peer_host_outer}" ]]; then
      echo "Skipping self: ${ha_peer_host_inner}"
      continue
    fi

    echo "Ensuring replication agreement exists to ${ha_peer_host_inner}..."
    if ! dsconf "${DSCONF_PARAMS[@]}" repl-agmt get "${ha_peer_host_inner}" --suffix "${DS_SUFFIX_NAME}" >/dev/null 2>&1; then
      dsconf "${DSCONF_PARAMS[@]}" repl-agmt create \
        --suffix "${DS_SUFFIX_NAME}" \
        --bind-dn cn=rmanager,cn=config \
        --bind-passwd "${BOOTSTRAP_RM_PASSWORD}" \
        --bind-method SIMPLE \
        --conn-protocol LDAP \
        --host "${ha_peer_host_inner}" \
        --port "${LDAP_PORT}" \
        "${ha_peer_host_inner}"
    else
      echo "Replication agreement already exists."
    fi

    # Initialize replication agreement only from non-primary replicas
    if [[ "${replica_id}" != 1 ]]; then
      echo "Initializing replication agreement to ${ha_peer_host_inner}..."
      dsconf "${DSCONF_PARAMS[@]}" repl-agmt init "${ha_peer_host_inner}" --suffix "${DS_SUFFIX_NAME}"
    fi
  done

done

echo
echo "✅ Bootstrap completed successfully."
