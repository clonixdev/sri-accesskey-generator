<?php

namespace DazzaDev\SriAccessKeyGenerator;

use Exception;
use DazzaDev\SriAccessKeyGenerator\Exceptions\AccessKeyException;

class AccessKeyGenerator
{
    private const SEQUENTIAL_LENGTH = 9;
    private const NUMERIC_CODE_MIN = 10000000;
    private const NUMERIC_CODE_MAX = 99999999;
    private const VERIFICATION_STRING_LENGTH = 48;
    private const EMISSION_TYPE = '1';
    private const DEFAULT_ESTABLISHMENT = '001';
    private const DEFAULT_EMISSION_POINT = '001';
    private const MODULUS = 11;
    private const MIN_MULTIPLIER = 2;
    private const MAX_MULTIPLIER = 7;

    /**
     * Generate an access key for the SRI XML
     *
     * @param array $data Array containing the required data for access key generation
     *                   - date: Emission date (string)
     *                   - ruc: RUC number (string)
     *                   - document_type: Document type code (string)
     *                   - environment_code: Environment code (string)
     *                   - sequential: Sequential number (string|int)
     *                   - establishment_code: Establishment code (optional, defaults to '001')
     *                   - emission_point_code: Emission point code (optional, defaults to '001')
     */
    public static function generate(array $data): string
    {
        try {
            $formattedDate = self::formatEmissionDate($data['date']);
            $documentType = $data['document_type'];
            $ruc = $data['ruc'];
            $environment = $data['environment_code'];
            $series = self::buildSeries($data);
            $sequential = self::formatSequential($data['sequential']);
            $numericCode = self::generateNumericCode();

            $verificationString = self::buildVerificationString(
                $formattedDate,
                $documentType,
                $ruc,
                $environment,
                $series,
                $sequential,
                $numericCode
            );

            $verificationDigit = self::calculateVerificationDigit($verificationString);

            return $verificationString . $verificationDigit;
        } catch (Exception $e) {
            throw new AccessKeyException('Failed to generate access key: ' . $e->getMessage());
        }
    }

    /**
     * Format the emission date to dmY format
     */
    private static function formatEmissionDate(string $date): string
    {
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            throw new AccessKeyException('Invalid date format');
        }

        return date('dmY', $timestamp);
    }

    /**
     * Build the series code from establishment and emission point
     */
    private static function buildSeries(array $data): string
    {
        $establishment = $data['establishment_code'] ?? self::DEFAULT_ESTABLISHMENT;
        $emissionPoint = $data['emission_point_code'] ?? self::DEFAULT_EMISSION_POINT;

        return $establishment . $emissionPoint;
    }

    /**
     * Format the sequential number with leading zeros
     */
    private static function formatSequential($sequential): string
    {
        return str_pad((string)$sequential, self::SEQUENTIAL_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Build the verification string by concatenating all components
     */
    private static function buildVerificationString(
        string $date,
        string $documentType,
        string $ruc,
        string $environment,
        string $series,
        string $sequential,
        int $numericCode
    ): string {
        return $date . $documentType . $ruc . $environment . $series . $sequential . $numericCode . self::EMISSION_TYPE;
    }

    /**
     * Calculate the verification digit using the modulus 11 algorithm
     */
    private static function calculateVerificationDigit(string $verificationString): int
    {
        $multiplier = self::MIN_MULTIPLIER;
        $sum = 0;

        for ($i = self::VERIFICATION_STRING_LENGTH - 1; $i >= 0; $i--) {
            if ($multiplier > self::MAX_MULTIPLIER) {
                $multiplier = self::MIN_MULTIPLIER;
            }

            $sum += (int)$verificationString[$i] * $multiplier;
            $multiplier++;
        }

        $remainder = $sum % self::MODULUS;
        $verificationDigit = $remainder === 0 ? 0 : self::MODULUS - $remainder;

        // Special case: if verification digit is 10, use 1 instead
        return $verificationDigit === 10 ? 1 : $verificationDigit;
    }

    /**
     * Generate a random 8-digit numeric code
     */
    private static function generateNumericCode(): int
    {
        return rand(self::NUMERIC_CODE_MIN, self::NUMERIC_CODE_MAX);
    }
}
