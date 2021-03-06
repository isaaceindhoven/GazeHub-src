<?php

declare(strict_types=1);

namespace ISAAC\GazeHub\Controllers;

use ISAAC\GazeHub\Decoders\TokenDecoder;
use ISAAC\GazeHub\Exceptions\DataValidationFailedException;
use ISAAC\GazeHub\Exceptions\TokenDecodeException;
use ISAAC\GazeHub\Exceptions\UnauthorizedException;
use ISAAC\GazeHub\Factories\JsonFactory;
use ISAAC\GazeHub\Models\Request;
use ISAAC\GazeHub\Repositories\ClientRepository;
use ISAAC\GazeHub\Services\DebugEmitter;
use React\Http\Message\Response;

use function array_key_exists;

class AuthController
{
    /**
     *  @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var TokenDecoder
     */
    private $tokenDecoder;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var DebugEmitter
     */
    private $debugEmitter;

    public function __construct(
        ClientRepository $clientRepository,
        TokenDecoder $tokenDecoder,
        JsonFactory $jsonFactory,
        DebugEmitter $debugEmitter
    ) {
        $this->clientRepository = $clientRepository;
        $this->tokenDecoder = $tokenDecoder;
        $this->jsonFactory = $jsonFactory;
        $this->debugEmitter = $debugEmitter;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws DataValidationFailedException
     * @throws TokenDecodeException
     */
    public function authenticate(Request $request): Response
    {
        $validatedData = $request->validate([
            'id' => 'required',
            'token' => 'required',
        ]);

        $client = $this->clientRepository->getById($validatedData['id']);

        if ($client === null) {
            return $this->jsonFactory->create(['error' => 'Not found'], 404);
        }

        $token = $this->tokenDecoder->decode($validatedData['token']);

        if (!array_key_exists('roles', $token)) {
            throw new DataValidationFailedException(['roles' => 'Roles are missing from token payload']);
        }

        $this->debugEmitter->emit('Authenticated', ['clientId' => $client->getId(), 'roles' => $token['roles']]);

        $client->setRoles($token['roles']);

        return $this->jsonFactory->create(['status' => 'Client authenticated'], 200);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws UnauthorizedException
     */
    public function unauthenticate(Request $request): Response
    {
        $request->isAuthorized();
        $id = $request->getAuthTokenFromHeader();

        $client = $this->clientRepository->getById($id);

        if ($client === null) {
            return $this->jsonFactory->create(['error' => 'Not found'], 404);
        }

        $this->debugEmitter->emit('Unauthenticated', ['clientId' => $client->getId()]);

        $client->setRoles([]);

        return $this->jsonFactory->create(['status' => 'Client unauthenticated'], 200);
    }
}
