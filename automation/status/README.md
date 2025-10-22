# status

![Version: 0.3.2](https://img.shields.io/badge/Version-0.3.2-informational?style=flat-square) ![Type: application](https://img.shields.io/badge/Type-application-informational?style=flat-square) ![AppVersion: v2.5.3](https://img.shields.io/badge/AppVersion-v2.5.3-informational?style=flat-square)

A Helm chart for Kubernetes

## How to install this chart

```console
helm install chart_name ./status
```

To install the chart with the release name `my-release`:

```console
helm install chart_name ./status
```

To install with some set values:

```console
helm install chart_name ./status --set values_key1=value1 --set values_key2=value2
```

To install with custom values file:

```console
helm install chart_name ./status -f values.yaml
```

## Requirements

| Repository | Name | Version |
|------------|------|---------|
| https://charts.bitnami.com/bitnami | postgresql | 11.6.20 |
| https://charts.bitnami.com/bitnami | redis | 17.3.5 |

## Values

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| affinity | object | `{}` |  |
| autoscaling.enabled | bool | `false` | Enable autoscaling for status service |
| autoscaling.maxReplicas | int | `3` | Maximum number of replicas for status service |
| autoscaling.minReplicas | int | `1` | Minimal number of replicas for status service |
| autoscaling.targetCPUUtilizationPercentage | int | `80` | Target CPU utilization for horizontal pod autoscaler |
| env.private | string | `nil` | Private environment variables which are stores in a kubernetes secret |
| env.public | object | `{"APP_DEBUG":false,"APP_ENV":"production","APP_LOG":"errorlog","APP_NAME":"statuspage","APP_TIMEZONE":"UTC","CACHET_BEACON":false,"CACHET_EMOJI":false,"CACHET_INTERNET_LOOKUPS":"false","CACHE_DRIVER":"redis","DB_DRIVER":"pgsql","DOCKER":true,"MAIL_ADDRESS":"","MAIL_DRIVER":"log","MAIL_ENCRYPTION":"tls","MAIL_HOST":"","MAIL_NAME":"","MAIL_PORT":25,"QUEUE_DRIVER":"redis","SESSION_DRIVER":"redis"}` | Default settings for cachet / status-service. See https://docs.cachethq.io/docs/installing-cachet for more information |
| existingSecret | string | `""` | Use existing secret for Cachet settings |
| extraLabels | object | `{}` |  |
| fullnameOverride | string | `""` |  |
| image.pullPolicy | string | `"IfNotPresent"` |  |
| image.repository | string | `"ghcr.io/dbildungsplattform/cachet"` | Image to use for deploying |
| imagePullSecrets | list | `[]` |  |
| ingress.annotations | object | `{}` | Additional annotations for the Ingress resource. To enable certificate autogeneration, place here your cert-manager annotations.  |
| ingress.enabled | bool | `false` | Enable ingress controller resource |
| ingress.hosts[0].host | string | `"chart-example.local"` |  |
| ingress.hosts[0].paths[0].path | string | `"/"` |  |
| ingress.hosts[0].paths[0].pathType | string | `"Prefix"` |  |
| ingress.tls | list | `[]` | Enable TLS configuration for the hostnames defined at ingress.hosts parameter |
| nameOverride | string | `""` |  |
| nodeSelector | object | `{}` |  |
| podAnnotations | object | `{}` | The annotations to be applied to instances of the status-service pod |
| podSecurityContext | object | `{}` | Settings for security context of the pod |
| postgresql | object | `{"auth":{"database":"status","existingSecret":"","password":"status","postgresPassword":"status","secretKeys":{"adminPasswordKey":"","userPasswordKey":""},"username":"status"},"enabled":true,"primary":{"persistence":{"enabled":true,"size":"10Gi"}},"service":{"ports":{"postgresql":5432}}}` | Postgresql settings. See https://github.com/bitnami/charts/tree/master/bitnami/postgresql fro details |
| redis | object | `{"architecture":"standalone","enabled":true}` | Redis settings. See https://github.com/bitnami/charts/tree/master/bitnami/redis fro details |
| replicaCount | int | `1` |  |
| resources.limits | object | `{"cpu":"1000m","memory":"1Gi"}` | The resources limits for the container |
| resources.requests | object | `{"cpu":"100m","memory":"128Mi"}` | The requested resources for the container |
| securityContext.runAsUser | int | `1001` |  |
| seedData | object | `{}` |  |
| service.port | int | `80` | Status service port |
| service.type | string | `"ClusterIP"` | Kubernetes Service type |
| serviceAccount.annotations | object | `{}` | Annotations to add to the service account |
| serviceAccount.create | bool | `false` | Specifies whether a service account should be created |
| serviceAccount.name | string | `""` | The name of the service account to use. If not set and create is true, a name is generated using the fullname template |
| test.enabled | bool | `true` |  |
| tolerations | list | `[]` |  |

