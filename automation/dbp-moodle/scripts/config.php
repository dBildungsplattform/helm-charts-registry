<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = getenv('MOODLE_DATABASE_TYPE');
$CFG->dblibrary = 'native';
$CFG->dbhost    = getenv('MOODLE_DATABASE_HOST');
$CFG->dbname    = getenv('MOODLE_DATABASE_NAME');
$CFG->dbuser    = getenv('MOODLE_DATABASE_USER');
$CFG->dbpass    = getenv('MOODLE_DATABASE_PASSWORD');
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => getenv('MOODLE_DATABASE_PORT_NUMBER'),
  'dbsocket' => '',
);

$_SERVER['HTTP_HOST'] = '{{ .Values.moodle.ingress.hostname }}';

$CFG->wwwroot   = 'https://{{ .Values.moodle.ingress.hostname }}';

$CFG->dataroot  = '/bitnami/moodledata';
$CFG->admin     = 'admin';
$CFG->directorypermissions = 02775;

{{- if .Values.redis.enabled }}
$CFG->session_handler_class = '\core\session\redis';
$CFG->session_redis_host = '{{ .Values.dbpMoodle.redis.host }}';
$CFG->session_redis_port = {{ .Values.dbpMoodle.redis.port }};
$CFG->session_redis_database = 0;
$CFG->session_redis_auth = '{{ .Values.dbpMoodle.redis.password }}';
$CFG->session_redis_prefix = 'mdl_';
$CFG->session_redis_acquire_lock_timeout = 60;
$CFG->session_redis_acquire_lock_warn = 0;
$CFG->session_redis_lock_expire = 7200;

$CFG->session_redis_serializer_use_igbinary = false;
$CFG->session_redis_compressor = 'none';
{{- end }}


{{- if .Values.dbpMoodle.phpConfig.pluginUIInstallation.enabled }}
$CFG->disableupdateautodeploy = false;
{{- else }}
$CFG->disableupdateautodeploy = true;
{{- end }}

{{ with .Values.dbpMoodle.phpConfig.additional -}}
{{- . }}
{{- end }}

require_once(__DIR__ . '/lib/setup.php');

{{- if .Values.dbpMoodle.phpConfig.extendedLogging }}
define('MDL_PERF' , true);
define('MDL_PERFDB' , true);
define('MDL_PERFTOLOG' , true); //OK for production
{{- end }}

{{ if .Values.dbpMoodle.phpConfig.debug -}}
@error_reporting(E_ALL | E_STRICT); // NOT FOR PRODUCTION SERVERS!
@ini_set('display_errors', '1');    // NOT FOR PRODUCTION SERVERS!
$CFG->debug = 32767;                // === DEBUG_DEVELOPER - NOT FOR PRODUCTION SERVERS!
$CFG->debugdisplay = 1;             // NOT FOR PRODUCTION SERVERS!
$CFG->debugpageinfo = 1;
$CFG->perfdebug = 7;
{{- else -}}
$CFG->debug = 0;
$CFG->debugdisplay = 0;
$CFG->debugpageinfo = 0;
$CFG->perfdebug = 7;
$CFG->debugsqltrace = 0;
{{- end }}

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!