<?php

namespace App\Controller;

use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Model\ExecIdStartPostBody;
use Docker\Docker;
use Docker\Stream\AttachWebsocketStream;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/domains")
 */
class DomainController extends AbstractController
{
    /**
     * @Route("/", name="domain_list")
     */
    public function index(): Response
    {
        $docker = Docker::create();

        $domain = 'test.localhost';
        $ctest = 'test.localhost';

        //$webSocketStream = $docker->containerAttachWebsocket('nginx', ['stdin' => true, 'stdout' => true, 'stream' => true, 'stderr' => true]);

        $exec = new ContainersIdExecPostBody();
        $exec->setUser('root:root');
        //$exec->setAttachStdout(true);
        //$exec->setCmd(['ls', '/etc/nginx/conf.d']);
        //$exec->setCmd(['rm', '/etc/nginx/conf.d/foo']);
        //$exec->setCmd(['chown', 'root:root','/etc/nginx/conf.d/test.conf', '&&', 'rm', '/etc/nginx/conf.d/test.conf', '&&', 'nginx', '-g', '\'daemon off;\'']);
        //$exec->setCmd(['nginx', '-g', '\'daemon off;\'']);

        $cmd = [
            'bash',
            '-c',
            'export OLDID=$(stat -c \'%u:%g\' /etc/nginx/conf.d)',
            '&& chown root:root /etc/nginx/conf.d',
            '&& chown root:root /etc/nginx/conf.d/test.conf',
            '&& rm -f /etc/nginx/conf.d/test.conf',
            '&& chown $OLDID /etc/nginx/conf.d',
            '&& unset($OLDID)',
            '&& nginx -g \'daemon off;\''
        ];

        $cmd = [
            'bash',
            '-c',
            'export OLDID=$(stat -c \'%u:%g\' /etc/nginx/conf.d)',
            '&& chown root:root /etc/nginx/conf.d',
            '&& cd /etc/nginx/conf.d',
            '&& chown root:root test.conf',
            '&& chattr -suSiadAc test.conf',
            '&& service nginx stop',
            '&& rm -f test.conf',
            '&& chown $OLDID /etc/nginx/conf.d',
            '&& unset($OLDID)',
            '&& nginx -g \'daemon off;\'',
        ];

        $exec->setCmd(['rm', '/etc/nginx/conf.d/test.conf']);

        //$docker->containerExec('nginx', $exec);

//        $cmd = ['rm', '-f', '/etc/nginx/conf.d/test.conf', '&&', 'service', 'nginx', 'restart'];
//        $exec = new ContainersIdExecPostBody();
//        $exec->setAttachStdout(true);
//        $exec->setCmd(['bash', '-c', implode(' ', $cmd)]);
//        $response = $docker->containerExec('nginx', $exec);
//        $response = $docker->execStart($response->getId(), new ExecIdStartPostBody(), Docker::FETCH_RESPONSE);

//        $file = sprintf('/etc/nginx/conf.d/%s.conf', $domain);
//        $cmd = [
//            'cp /etc/nginx/conf.d/template ' . $file,
//            sprintf('sed -i "s/DOMAIN_NAME/%s/" %s', $domain, $file),
//            sprintf('sed -i "s/CONTAINER_NAME/%s/" %s', $ctest, $file),
//            'sleep 2',
//            'service nginx restart'
//        ];
//        $exec = new ContainersIdExecPostBody();
//        //$exec->setAttachStdout(true);
//        $exec->setCmd(['bash', '-c', implode(' && ', $cmd)]);
//        $response = $docker->containerExec('nginx', $exec);
//        $response = $docker->execStart($response->getId(), new ExecIdStartPostBody(), Docker::FETCH_RESPONSE);

//        $response = $docker->execInspect($response->getId(), Docker::FETCH_RESPONSE);
//
//        $exec = new ContainersIdExecPostBody();
//        $exec->setAttachStdout(true);
//        $exec->setCmd(['service', 'nginx', 'restart']);
//
//        $response = $docker->containerExec('nginx', $exec);

        //$webSocketStream->write('ls -al /etc/nxinx/conf.d');

        //$execId = new ExecIdStartPostBody();
        //$response = $docker->execStart($response->getId(), $execId, Docker::FETCH_RESPONSE);

        //$response = $docker->execInspect($response->getId(), Docker::FETCH_RESPONSE);

        // https://ahmet.im/blog/docker-logs-api-binary-format-explained/
        //$data = trim(mb_substr($response->getBody()->getContents(), 8));

        $data = '';

        return $this->render('domain/index.html.twig', [
            'data' => explode("\n", $data),
        ]);
    }

    /**
     * @Route("/create/{domain}/{container}", name="domain_create")
     *
     * @param string $domain
     * @param string $container
     * @return Response
     */
    public function create(string $domain, string $container): Response
    {
        $workDir = '/var/www/workspace/' . $domain;

        $docker = Docker::create();

        $cmd = [
            'mkdir -p ' . $workDir,
            'chown 1000:1000 ' . $workDir
        ];

        $exec = new ContainersIdExecPostBody();
        $exec->setCmd(['bash', '-c', implode(' && ', $cmd)]);
        $response = $docker->containerExec('admin', $exec);
        $docker->execStart($response->getId(), new ExecIdStartPostBody(), Docker::FETCH_RESPONSE);

        $file = sprintf('/etc/nginx/conf.d/%s.conf', $domain);

        $cmd = [
            'cp /etc/nginx/conf.d/template ' . $file,
            sprintf('sed -i "s/DOMAIN_NAME/%s/" %s', $domain, $file),
            sprintf('sed -i "s/CONTAINER_NAME/%s/" %s', $container, $file),
            'sleep 2',
            'service nginx restart'
        ];

        $exec = new ContainersIdExecPostBody();
        $exec->setCmd(['bash', '-c', implode(' && ', $cmd)]);
        $response = $docker->containerExec('nginx', $exec);
        $docker->execStart($response->getId(), new ExecIdStartPostBody(), Docker::FETCH_RESPONSE);

        return $this->redirectToRoute('domain_list');
    }
}
