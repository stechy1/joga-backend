<?php


namespace app\middleware;


use app\model\http\IRequest;
use app\model\http\IResponse;
use app\model\manager\jwt\JWTManager;
use app\model\util\StatusCodes;
use Exception;
use Logger;

/**
 * Class AuthMiddleware
 * @Inject JWTManager
 * @package app\middleware
 */
class AuthMiddleware implements IMiddleware {

    public const JWT_DATA = "jwt_data";

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var JWTManager
     */
    private $jwtmanager;

    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function apply(IRequest $request, IResponse $response): void {
        $headers = $request->getHeaders();
        $this->logger->trace($headers);
        if (!isset($headers['authorization']) && !isset($headers['Authorization'])) {
            $this->logger->trace("Žádná autorizační hlavička není k dispozici.");
            return;
        }
        $this->logger->trace("Autorizační token je k dispozici.");

        $jwt = isset($headers['authorization'])
            ? $headers['authorization']
            : $headers['Authorization'];

        try {
            $this->logger->trace("Parsuji JWT.");
            $jwtData = $this->jwtmanager->decodeJWT($jwt);
            $this->logger->trace($jwtData);
            $response->addFlowData(self::JWT_DATA, $jwtData);
        } catch (Exception $ex) {
            $response->setCode(StatusCodes::UNAUTHORIZED);
            throw new MiddlewareException("Nepodařilo se naparsovat JWT!");
        }
    }

}
