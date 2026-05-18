<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'KeluargaKas API',
    description: 'API Backend untuk aplikasi manajemen keuangan keluarga KeluargaKas. Dibangun dengan Laravel 13 + Supabase PostgreSQL.',
    contact: new OA\Contact(email: 'dev@keluargakas.app'),
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Masukkan token Sanctum yang diperoleh dari endpoint login.',
)]
#[OA\Server(url: 'http://localhost:8000', description: 'Local Development Server')]
#[OA\Schema(
    schema: 'DebtResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'source', type: 'string', example: 'KPR Bank BCA'),
        new OA\Property(property: 'monthly_cost', type: 'number', example: 2500000),
        new OA\Property(property: 'monthly_deadline', type: 'integer', example: 15),
        new OA\Property(property: 'total_tenor', type: 'integer', example: 120),
        new OA\Property(property: 'total_paid', type: 'number', example: 5000000),
        new OA\Property(property: 'remaining', type: 'number', example: 295000000),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'TransactionResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'type', type: 'string', enum: ['in', 'out'], example: 'out'),
        new OA\Property(property: 'amount', type: 'number', example: 500000),
        new OA\Property(property: 'category', type: 'string', example: 'Cicilan KPR'),
        new OA\Property(property: 'date', type: 'string', format: 'date', example: '2025-01-15'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'receipt_url', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class SwaggerController extends Controller
{
    // Controller ini hanya sebagai container untuk anotasi OpenAPI global.
}
