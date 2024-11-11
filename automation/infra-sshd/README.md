# INFRA-SSHD
A bastion host (also jump server or jump service) is usually set up as a single entrypoint into a privat system. In IONOS' case, a SSH daemon grants access to a DMZ which contains PostgreSQL and MongoDB databases.

## TL;DR;
```
$ helm upgrade infra-sshd ./infra-sshd --install --create-namespace -n sshd-service
```

## Introduction
This chart creates an SSH daemon deployment on Kubernetes. The daemon pulls in authorized keys which allows respective user to create an SSH tunnel. No further actions are permitted, no shell is accessible.

The image being pulled can be found at https://hub.docker.com/repository/docker/schulcloud/infra-sshd and https://github.com/hpi-schul-cloud/infra-sshd .

This chart can be installed in two flavors:
- Standalone: If standalone is set to true the service will be available within the cluster and can be exposed by a complementary ingress resource.
- Behind an HAProxy (e.g. Cronon managed): If standalone is set to false the service is exposed on node ports on which traffic is usually restricted by  policies. This setup however is required behind a Cronon managed HAProxy.

**Attention: The setting needs to be verified so it matches the particular instance!**

## Installing the chart
Prior to installing, please update the authorized keys file. This file is used to create a configmap which eventually tells the daemon which keys to grant access. Currently, only a "support" user is configured in the image. For scalability reasons, authorized key files are separated by users, e.g. user support: support_authorized_keys.
```
$ helm upgrade infra-sshd ./infra-sshd --install --create-namespace -n sshd-service
```

## Uninstalling the chart
```
$ helm -n sshd-service delete infra-sshd
```

## Parameters
These parameters can be set:

| Parameter          | Description                                                     | Default         |
| ------------------ | --------------------------------------------------------------- | --------------- |
| replicaCount       | Count of pods that will be created as part of the deployment    | 1               |
| image.repository   | Repository the image will be pulled from                        | schulcloud/infra-sshd |
| image.tag          | Image tag which will be used                                    | stable          |
| ingress.standalone | Specifies whether INFRA-SSHD is deployed behind HAProxy or standalone | false           |

## Authorized keys
Currently supported users and key files:

| User    | Authorized keys file    | Mount path                         |
| ------- | ----------------------- | ---------------------------------- |
| support | support_authorized_keys | /home/support/.ssh/authorized_keys |
