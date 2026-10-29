<?php

declare(strict_types=1);

namespace App\controllers;

use App\Models\Curso;
use Illuminate\Database\QueryException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Cursos
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function read(Request $request, Response $response, array $args): Response
    {
        $this->container->get('eloquent');

        if (isset($args['id'])) {
            $cursoId = filter_var($args['id'], FILTER_VALIDATE_INT);
            if ($cursoId === false || $cursoId < 1) {
                return $this->json($response, ['error' => 'ID inválido.'], 422);
            }

            $curso = Curso::find($cursoId);
            if ($curso === null) {
                return $this->json($response, ['error' => 'Curso no encontrado.'], 404);
            }

            return $this->json($response, ['data' => $curso->toArray()], 200);
        }

        $cursos = Curso::all()->toArray();
        return $this->json($response, ['data' => $cursos], 200);
    }

    public function create(Request $request, Response $response, array $args): Response
    {
        $this->container->get('eloquent');
        $data = $request->getParsedBody();
        $data = is_array($data) ? $data : [];

        $codigo = trim((string) ($data['codigo_curso'] ?? ''));
        $nombre = trim((string) ($data['nombre'] ?? ''));
        $creditos = filter_var($data['creditos'] ?? null, FILTER_VALIDATE_INT);
        $cupo = filter_var($data['cupo_maximo'] ?? null, FILTER_VALIDATE_INT);
        $estado = isset($data['estado']) ? (bool) $data['estado'] : true;

        try {
            $curso = Curso::create([
                'codigo_curso' => $codigo,
                'nombre' => $nombre,
                'creditos' => $creditos,
                'cupo_maximo' => $cupo,
                'estado' => $estado
            ]);
        } catch (QueryException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
                return $this->json($response, ['error' => 'Código ya registrado.'], 409);
            }
            throw $exception;
        }

        return $this->json($response, ['data' => $curso->toArray()], 201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $this->container->get('eloquent');
        if (!isset($args['id'])) {
            return $this->json($response, ['error' => 'ID requerido.'], 400);
        }

        $cursoId = filter_var($args['id'], FILTER_VALIDATE_INT);
        if ($cursoId === false || $cursoId < 1) {
            return $this->json($response, ['error' => 'ID inválido.'], 422);
        }

        $curso = Curso::find($cursoId);
        if ($curso === null) {
            return $this->json($response, ['error' => 'Curso no encontrado.'], 404);
        }

        $data = $request->getParsedBody();
        $data = is_array($data) ? $data : [];

        if (isset($data['codigo_curso'])) {
            $curso->codigo_curso = trim((string) $data['codigo_curso']);
        }
        if (isset($data['nombre'])) {
            $curso->nombre = trim((string) $data['nombre']);
        }
        if (isset($data['creditos'])) {
            $creditos = filter_var($data['creditos'], FILTER_VALIDATE_INT);
            if ($creditos !== false && $creditos > 0) {
                $curso->creditos = $creditos;
            }
        }
        if (isset($data['cupo_maximo'])) {
            $cupo = filter_var($data['cupo_maximo'], FILTER_VALIDATE_INT);
            if ($cupo !== false && $cupo > 0) {
                $curso->cupo_maximo = $cupo;
            }
        }
        if (isset($data['estado'])) {
            $curso->estado = (bool) $data['estado'];
        }

        try {
            $curso->save();
        } catch (QueryException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
                return $this->json($response, ['error' => 'Código ya registrado.'], 409);
            }
            throw $exception;
        }

        return $this->json($response, ['data' => $curso->toArray()], 200);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $this->container->get('eloquent');
        if (!isset($args['id'])) {
            return $this->json($response, ['error' => 'ID requerido.'], 400);
        }

        $cursoId = filter_var($args['id'], FILTER_VALIDATE_INT);
        if ($cursoId === false || $cursoId < 1) {
            return $this->json($response, ['error' => 'ID inválido.'], 422);
        }

        $curso = Curso::find($cursoId);
        if ($curso === null) {
            return $this->json($response, ['error' => 'Curso no encontrado.'], 404);
        }

        $curso->delete();

        return $this->json($response, ['message' => 'Curso eliminado.'], 200);
    }

    private function json(Response $response, array $payload, int $status): Response
    {
        $response->getBody()->write(json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        ));

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus($status);
    }
}
