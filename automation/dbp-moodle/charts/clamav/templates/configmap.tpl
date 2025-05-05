---
kind: ConfigMap
apiVersion: v1
metadata:
  name: {{ include "common.names.fullname" . }}
  labels: {{ include "common.labels.standard" . | nindent 4}}
data:
  clamd.conf: |
    LogFile /dev/null
    LogVerbose yes
    DatabaseDirectory /opt/app-root/src
    LocalSocket /tmp/clamd.sock
    PidFile /tmp/clamd.pid
    TCPAddr 0.0.0.0
    TCPSocket 3310
    StreamMaxLength 1000M
    Foreground yes
    Debug {{ .Values.settings.debug }}