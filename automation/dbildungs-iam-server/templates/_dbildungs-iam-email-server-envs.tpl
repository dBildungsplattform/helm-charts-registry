{{- define "dbildungs-iam-server-email-backend-envs" }}
- name: DB_SECRET
  valueFrom:
    secretKeyRef:
        name: {{ default .Values.auth.existingSecret .Values.auth.secretName }}
        key: db-password
- name: DB_USERNAME
  valueFrom:
    secretKeyRef:
        name: {{ default .Values.auth.existingSecret .Values.auth.secretName }}
        key: db-username
- name: DB_HOST
  valueFrom:
    secretKeyRef:
        name: {{ default .Values.auth.existingSecret .Values.auth.secretName }}
        key: db-host
- name: DB_CLIENT_URL
  value: "postgres://$(DB_HOST)/"
- name: KC_ADMIN_SECRET
  valueFrom:
    secretKeyRef:
        name: {{ default .Values.auth.existingSecret .Values.auth.secretName }}
        key: keycloak-adminSecret
{{- end}}
