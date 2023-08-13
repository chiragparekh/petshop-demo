<?php

namespace App\Services;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\UnencryptedToken;

class JwtService
{
    public function __construct(
        private JwtFacade $jwtFacade,
        private Sha256 $sha256,
        private JoseEncoder $joseEncoder
    )
    {}

    public function generate(string $userUuid): string
    {
        $signerKey = InMemory::base64Encoded(config('app.jwt.key'));

        $token = $this->jwtFacade->issue(
            $this->sha256,
            $signerKey,
            static fn (
                Builder $builder,
                \DateTimeImmutable $issuedAt
            ): Builder => $builder
                ->issuedBy(config('app.url'))
                ->withClaim('userUuid', $userUuid)
                ->expiresAt($issuedAt->modify('+60 minutes'))
        );

        return $token->toString();
    }

    public function parse(string $token): UnencryptedToken | bool
    {
        $tokenParser = new Parser($this->joseEncoder);

        try {
            return $tokenParser->parse($token);
        } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
            return false;
        }
    }
}