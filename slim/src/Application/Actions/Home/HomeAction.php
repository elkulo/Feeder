<?php
declare(strict_types=1);

namespace App\Application\Actions\Home;

use App\Application\Settings\SettingsInterface;
use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class HomeAction extends Action
{

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var Twig
     */
    protected $twig;

    /**
     * @param LoggerInterface $logger
     * @param SettingsInterface $settings
     * @param Twig $twig
     */
    public function __construct(LoggerInterface $logger, SettingsInterface $settings, Twig $twig)
    {
        parent::__construct($logger);
        $this->settings = $settings;
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {

        $this->logger->info('Home was viewed.');

        $twig = $this->twig;
        $settings = $this->settings;

        // bodyを生成
        $response = $twig->render($this->response, 'home.twig', [
            'title' => $settings->get('site.title'),
            'description' => $settings->get('site.description'),
            'robots' => $settings->get('site.robots')
        ]);

        return $response;
    }
}
