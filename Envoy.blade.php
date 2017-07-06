@setup
$site_name = 'meat';
$ssh_user = 'forge';
$server_ip = '159.203.184.9';
$server_base_path = '/home/forge/' . $site_name . '.dmeat.cl';
//$sync_folder =  '/storage/app/'; // Laravel | Based on the base path of the project
$sync_folder =  '/htdocs/content/uploads/'; // Themosis | Based on the base path of the project


$ssh_connection = $ssh_user.'@'.$server_ip;
@endsetup

@servers(['server' => [$ssh_connection], 'local' => '127.0.0.1'])

@task('setup', ['on' => 'local'])
echo "<info>Installing composer dependencies...</info>";
composer install --prefer-dist
echo "<info>Installing NPM dependencies...</info>";
npm install
echo "<info>Compiling assets...</info>";
npm run watch
@endtask

@story('sync')
db:backup
db:update_local
db:delete_dump_server
db:delete_dump_local
pull_images
@endstory

@story('sync_database')
db:backup
db:update_local
db:delete_dump_server
db:delete_dump_local
@endstory

@task('show_env', ['on' => 'server'])
cat {{ $server_base_path }}/.env
@endtask

@task('db:backup', ['on' => 'server'])
cd {{ $server_base_path }}

export DB_HOST=$(php vendor/bin/envoy_helper get_env DB_HOST)
export DB_PASS=$(php vendor/bin/envoy_helper get_env DB_PASSWORD)
export DB_USER=$(php vendor/bin/envoy_helper get_env DB_USER)
export DB_NAME=$(php vendor/bin/envoy_helper get_env DB_NAME)

mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASS -c -e --default-character-set=utf8 --single-transaction --skip-set-charset --add-drop-database $DB_NAME | gzip -c > {{ $site_name }}.sql.gz
echo "Database dumped on server"
@endtask

@task('db:update_local', ['on' => 'local'])
if [ -f {{ $site_name }}.sql ]; then
    echo "File {{ $site_name }}.sql deleted"
    rm {{ $site_name }}.sql
fi

if [ -f {{ $site_name }}.sql.gz ]; then
    echo "File {{ $site_name }}.sql.gz deleted"
    rm {{ $site_name }}.sql.gz
fi

echo "Downloading database backup from server..."
scp {{ $ssh_connection }}:{{ $server_base_path }}/{{ $site_name }}.sql.gz ./
echo "Database dump downloaded";
gzip -d {{ $site_name }}.sql.gz

echo "Importing backup in local database..."
export DB_HOST=$(php vendor/bin/envoy_helper get_env DB_HOST)
export DB_PASS=$(php vendor/bin/envoy_helper get_env DB_PASSWORD)
export DB_USER=$(php vendor/bin/envoy_helper get_env DB_USER)
export DB_NAME=$(php vendor/bin/envoy_helper get_env DB_NAME)

if [ -z "$DB_PASS" ]; then
    echo "importing without mysql password"
    mysql -h $DB_HOST -u $DB_USER $DB_NAME < {{ $site_name }}.sql --binary-mode
else
    mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME < {{ $site_name }}.sql --binary-mode
fi
echo "<info>Local database updated</info>"
@endtask

@task('db:delete_dump_server', ['on' => 'server'])
cd {{ $server_base_path }}
if [ -f {{ $site_name }}.sql.gz ]; then
    rm {{ $site_name }}.sql.gz
fi
echo "Database dump deleted"
@endtask


@task('db:delete_dump_local', ['on' => 'local'])
if [ -f {{ $site_name }}.sql ]; then
    rm {{ $site_name }}.sql
fi
echo "Database dump deleted from local"
@endtask

@task('pull_images', ['on' => 'local'])
echo "Syncing images..."
rsync -avzP --exclude=bfi_thumb --exclude=cache {{ $ssh_connection }}:{{ $server_base_path }}{{ $sync_folder }} .{{ $sync_folder }}
echo "Images Synced"
@endtask