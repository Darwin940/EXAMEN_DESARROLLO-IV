<?php

declare(strict_types=1);

namespace App\Validators;

use Respect\Validation\ValidatorBuilder as v;

final class CursoValidator
{
    private function reglas(): array
    {
        return [
            'codigo_curso' => [
                'regla' => v::stringType()->notBlank()->length(v::between(2, 20)),
                'mensaje' => 'Código requiere entre 2 a 20 caracteres)',
            ],
            'nombre' => [
                'regla' => v::stringType()->notBlank()->length(v::between(3, 100)),
                'mensaje' => 'Nombre requiere entre 3 a 100 caracteres',
            ],
            'creditos' => [
                'regla' => v::intVal()->positive(),
                'mensaje' => 'Créditos deben ser entero positivo',
            ],
            'cupo_maximo' => [
                'regla' => v::intVal()->positive(),
                'mensaje' => 'Cupo debe ser entero positivo',
            ],
            'estado' => [
                'regla' => v::boolVal(),
                'mensaje' => 'Estado debe ser verdadero o falso',
            ],
        ];
    }

    public function validate(array $data, bool $isUpdate = false): array
    {
        $errores = [];

        foreach ($this->reglas() as $campo => $config) {
            $valor = $data[$campo] ?? null;
            $regla = $config['regla'];
            $mensaje = $config['mensaje'];

            if ($isUpdate && !array_key_exists($campo, $data)) {
                continue;
            }

            if ($campo === 'estado' && !array_key_exists('estado', $data)) {
                continue;
            }

            if ($regla->validate($valor)->hasFailed()) {
                $errores[$campo] = $mensaje;
            }
        }

        return $errores;
    }
}
