<?php

namespace App\Http\Controllers;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Hyperhire Assessment API Documentation",
 * description="Base API for Hypehire Assessment",
 * @OA\Contact(
 * email="support@ajulity.com"
 * )
 * )
 * 
 * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST,
 * description="Hyperhire Assessment API Server"
 * )
 * 
 */
abstract class Controller
{
    //
}
