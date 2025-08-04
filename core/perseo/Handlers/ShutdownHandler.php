<?php

declare(strict_types=1);

namespace PerSeo\Handlers;

use Slim\Handlers\ErrorHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\ResponseEmitter;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ShutdownHandler
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var HttpErrorHandler
     */
    private $errorHandler;

    /**
     * @var bool
     */
    private $displayErrorDetails;
    
    protected $logger;
    
    protected $container;
    
    protected $settings;
    
    protected $reporting;

    /**
     * ShutdownHandler constructor.
     *
     * @param Request            $request
     * @param HttpErrorHandler   $errorHandler
     * @param LoggerInterface    $logger
     * @param ContainerInterface $container
     * @param bool               $displayErrorDetails
     */
     
    public function __construct(Request $request, ErrorHandler $errorHandler, LoggerInterface $logger, ContainerInterface $container, bool $displayErrorDetails)
    {
        $this->request = $request;
        $this->errorHandler = $errorHandler;
        $this->displayErrorDetails = $displayErrorDetails;
        $this->logger = $logger;
        $this->settings = ($container->has('settings_error') ? $container->get('settings_error') : ['reporting' => ['E_ALL'], 'display_error_details' => false, 'log_errors' => false, 'log_error_details' => false]);
        $this->reporting = (isset($this->settings['reporting']) ? $this->settings['reporting'] : ['E_ALL']);
    }

    public function __invoke()
    {
        $error = error_get_last();
        if ($error) {
            $errorFile = $error['file'];
            $errorLine = $error['line'];
            $errorMessage = $error['message'];
            $errorType = $error['type'];
            $message = 'An error while processing your request. Please try again later.';

            if ($this->displayErrorDetails) {
                switch ($errorType) {
                    case E_USER_ERROR:
                        if (!in_array('~E_ERROR', $this->reporting) && (in_array('E_ALL', $this->reporting) || in_array('E_ERROR', $this->reporting))) {
                            $message = "FATAL ERROR: {$errorMessage}. ";
                            $message .= " on line {$errorLine} in file {$errorFile}.";
                            $this->logger->error($message);
                        }
                        break;

                    case E_USER_WARNING:
                        if (!in_array('~E_WARNING', $this->reporting) && (in_array('E_ALL', $this->reporting) || in_array('E_WARNING', $this->reporting))) {
                            $message = "WARNING: {$errorMessage}";
                            $this->logger->warning($message);
                        }
                        break;

                    case E_USER_NOTICE:
                        if (!in_array('~E_NOTICE', $this->reporting) && (in_array('E_ALL', $this->reporting) || in_array('E_NOTICE', $this->reporting))) {
                            $message = "NOTICE: {$errorMessage}";
                            $this->logger->notice($message);
                        }
                        break;

                    default:
                        if (in_array('E_ALL', $this->reporting)) {
                            $message = "ERROR: {$errorMessage}";
                            $message .= " on line {$errorLine} in file {$errorFile}.";
                            $this->logger->error($message);
                        }
                        break;
                }
            }

            $exception = new HttpInternalServerErrorException($this->request, $message);
            $response = $this->errorHandler->__invoke($this->request, $exception, $this->displayErrorDetails, false, false);
            
            if (ob_get_length()) {
                ob_clean();
            }

            $responseEmitter = new ResponseEmitter();
            $responseEmitter->emit($response);
        }
    }
}
