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
    protected $view;

    /**
     * @param LoggerInterface $logger
     * @param SettingsInterface $settings
     * @param Twig $twig
     */
    public function __construct(LoggerInterface $logger, SettingsInterface $settings, Twig $twig)
    {
        parent::__construct($logger);
        $this->settings = $settings;
        $this->view = $twig;
    }

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {

        $this->logger->info('Home was viewed.');

        return $this->view->render($this->response, 'home.twig', [
            'title' => $this->settings->get('site.title'),
            'description' => $this->settings->get('site.description'),
            'robots' => $this->settings->get('site.robots')
        ]);
    }
}
