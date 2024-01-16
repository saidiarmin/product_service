<?php

namespace App\Normalizer;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ErrorNormalizer implements NormalizerInterface
{

    public function normalize(mixed $exception, string $format = null, array $context = []): array
    {
        return [
            'message' => $context['debug'] ? $exception->getMessage() : 'An error occurred',
            'status' => $exception->getStatusCode(),
            'trace' => $context['debug'] ? $exception->getTrace() : [],
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof FlattenException;
    }

    public function getSupportedTypes(?string $format): array
    {
        return ['json'];
    }
}