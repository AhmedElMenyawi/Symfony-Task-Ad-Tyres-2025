<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Exception\RuntimeException;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

class EncryptionService
{
    private string $encryptionKey;
    private string $cipher = 'aes-256-gcm';
    private int $tagLength = 16;
    private int $keyVersion = 1; // For key rotation support

    public function __construct(string $appSecret)
    {
        if (empty($appSecret)) {
            throw new RuntimeException('Application secret is not configured');
        }
        
        // Generate a deterministic but secure key from the app secret
        $this->encryptionKey = hash('sha256', $appSecret, true);
        
        // Validate key length
        if (strlen($this->encryptionKey) !== 32) {
            throw new InvalidArgumentException('Generated encryption key must be 32 bytes (256 bits)');
        }
    }

    public function encrypt(string $data): string
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Data cannot be empty');
        }

        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        
        $tag = '';
        $encrypted = openssl_encrypt(
            $data,
            $this->cipher,
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            $this->tagLength
        );

        if ($encrypted === false) {
            throw new RuntimeException('Encryption failed');
        }

        // Format: version(1) + iv(12) + tag(16) + encrypted_data
        return base64_encode(chr($this->keyVersion) . $iv . $tag . $encrypted);
    }

    public function decrypt(string $encryptedData): string
    {
        if (empty($encryptedData)) {
            throw new InvalidArgumentException('Encrypted data cannot be empty');
        }

        $data = base64_decode($encryptedData);
        if ($data === false) {
            throw new RuntimeException('Invalid encrypted data format');
        }

        // Extract components
        $version = ord(substr($data, 0, 1));
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = substr($data, 1, $ivlen);
        $tag = substr($data, $ivlen + 1, $this->tagLength);
        $encrypted = substr($data, $ivlen + $this->tagLength + 1);

        // Verify version compatibility
        if ($version !== $this->keyVersion) {
            throw new RuntimeException('Incompatible encryption version');
        }

        $decrypted = openssl_decrypt(
            $encrypted,
            $this->cipher,
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($decrypted === false) {
            throw new RuntimeException('Decryption failed');
        }

        return $decrypted;
    }

    /**
     * Mask sensitive data for display
     * Only shows last 4 digits of card number
     */
    public function maskCardNumber(string $cardNumber): string
    {
        return str_repeat('*', strlen($cardNumber) - 4) . substr($cardNumber, -4);
    }

    /**
     * Mask CVV for display
     * Always shows as ***
     */
    public function maskCvv(string $cvv): string
    {
        return '***';
    }

    /**
     * Mask expiration date for display
     * Only shows month and last digit of year
     */
    public function maskExpirationDate(string $expirationDate): string
    {
        return substr($expirationDate, 0, 2) . '/**';
    }
} 