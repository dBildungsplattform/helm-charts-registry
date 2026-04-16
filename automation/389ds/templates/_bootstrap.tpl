{{- /*
Bootstrap container template for bootstrap
*/}}
{{- define "389ds.bootstrapEnv" }}
- name: BOOTSTRAP_HA_PEER_HOSTS
  value: {{ .Values.extendedBootstrap.peers | quote }}
- name: BOOTSTRAP_BACKEND_NAME
  value: {{ .Values.extendedBootstrap.backendName | quote }}
- name: BOOTSTRAP_IMPORT_LDIF
  value: {{ .Values.extendedBootstrap.importLdif | empty | not | quote }}
- name: BOOTSTRAP_CONFIG_OVERRIDES
  value: {{ .Values.extendedBootstrap.configOverrides | quote }}
- name: BOOTSTRAP_RM_PASSWORD
  valueFrom:
    secretKeyRef:
      name: {{ .Values.extendedBootstrap.rmPassword.secretName | quote }}
      key: {{ .Values.extendedBootstrap.rmPassword.secretKey | quote }}
{{- end }}

{{- define "389ds.bootstrapVolume" }}
- name: bootstrap-scripts
  configMap:
    name: {{ include "389ds.fullname" . }}-bootstrap
    defaultMode: 0555
{{- end }}

{{- define "389ds.bootstrapVolumeMount" }}
- name: bootstrap-scripts
  mountPath: /scripts
{{- end }}

