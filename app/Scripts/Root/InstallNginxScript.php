<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Facades\ConfigView;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class InstallNginxScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $nginxDir = '/etc/nginx';

        $this->init($server, $ssh);

        /*
         * TODO: IMPORTANT! Figure out how to verify that Nginx is installed and is managed by systemd.
         *       Also, figure out what to do if something fails here, like in all other scripts.
         */

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get update -y');
        $this->read();

        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get install -y nginx');
        $this->read();

        $this->disablePty();

        // Create a nologin system user to run Nginx workers as configured in nginx.conf.
        $this->exec('useradd -r -s /usr/sbin/nologin nginx');

        // Carefully remove the default config files that we don't need.
        foreach ([
            'nginx.conf',
            'fastcgi.conf',
            'fastcgi_params',
            'gzip.conf',
            'koi-utf',
            'koi-win',
            'mime.types',
            'proxy_params',
            'scgi_params',
            'uwsgi_params',
            'win-utf',
        ] as $file) {
            $this->exec("rm -f {$nginxDir}/{$file}");
        }

        // Remove all "sites" that exist in Nginx config out of the box.
        $this->exec("rm -rf {$nginxDir}/sites-enabled/* && rm -rf {$nginxDir}/sites-available/*");

        // Send all the custom Nginx config files to their intended locations.
        foreach ([
            'nginx.conf' => 'nginx',
            'proxy_params' => 'proxy_params',
            // Yes, we're not using UWSGI or FastCGI for deployments,
            // but I'd still like to have the configs on the servers just in case
            // and for the possible future use.
            'uwsgi_params' => 'uwsgi_params',
            'fastcgi.conf' => 'fastcgi',
            'mime_types' => 'mime_types',
            'gzip.conf' => 'gzip',
            'ssl_params' => 'ssl_params',
            'ssl-dhparams.pem' => 'ssl-dhparams',
        ] as $file => $view) {
            $this->sendString("{$nginxDir}/{$file}", ConfigView::render("nginx.{$view}"));
        }

        // TODO: IMPORTANT! Have a Michman-branded static page showing up when there's no project set up on a server (I already have one when there is.), if that server is "app" or "web".
    }
}
