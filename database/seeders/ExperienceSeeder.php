<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\ExperienceParameter;
use App\Models\JobType;
use App\Models\Material;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $materials = Material::all()->keyBy('name');
        $jobTypes = JobType::all()->keyBy('name');

        $experiences = [
            // MDF 3mm con láser 10W - varias experiencias para probar info suficiente
            [
                'title' => 'Grabado foto en MDF 3mm',
                'material' => 'MDF',
                'job_type' => 'Grabado',
                'thickness_mm' => 3.0,
                'machine' => 'Sculpfun S9',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Grabado de fotografía en escala de grises sobre MDF de 3mm.',
                'results' => 'Buen contraste, detalle fino visible. Algo de carbonizado en bordes.',
                'observations' => 'Usar asistencia de aire para reducir carbonizado.',
                'status' => 'approved',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '3000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '25', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'LPI', 'value' => '300', 'unit' => 'lpi'],
                        ['name' => 'Modo', 'value' => 'Escala de grises', 'unit' => ''],
                    ],
                ],
            ],
            [
                'title' => 'Corte MDF 3mm - Piezas para maquetas',
                'material' => 'MDF',
                'job_type' => 'Corte',
                'thickness_mm' => 3.0,
                'machine' => 'Sculpfun S9',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Corte de piezas pequeñas para maquetas arquitectónicas.',
                'results' => 'Corte limpio, piezas encajan bien. Borde ligeramente carbonizado.',
                'observations' => 'Mesa de panal recomendada. Velocidad baja para corte completo.',
                'status' => 'approved',
                'parameters' => [
                    'Corte' => [
                        ['name' => 'Velocidad', 'value' => '400', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '85', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '3', 'unit' => 'unidad'],
                        ['name' => 'Frecuencia', 'value' => '20', 'unit' => 'kHz'],
                    ],
                ],
            ],
            [
                'title' => 'Grabado texto fino MDF 3mm',
                'material' => 'MDF',
                'job_type' => 'Grabado',
                'thickness_mm' => 3.0,
                'machine' => 'Ortur Laser Master 2',
                'machine_power_w' => 10,
                'software' => 'LaserGRBL',
                'description' => 'Grabado de texto pequeño (fuente 6pt) en MDF.',
                'results' => 'Texto legible, buena definición. Ligeramente quemado en esquinas agudas.',
                'observations' => 'Reducir potencia para fuentes muy pequeñas.',
                'status' => 'approved',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '4000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '20', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'DPI', 'value' => '500', 'unit' => 'dpi'],
                    ],
                ],
            ],
            [
                'title' => 'Grabado y corte MDF 3mm - Llaveros',
                'material' => 'MDF',
                'job_type' => 'Grabado y corte',
                'thickness_mm' => 3.0,
                'machine' => 'Atomstack A10 Pro',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Llaveros personalizados con grabado de nombre y corte de forma.',
                'results' => 'Buen resultado general. Grabado uniforme, corte completo.',
                'observations' => 'Hacer grabado antes que corte. Usar cinta de enmascarar para proteger superficie.',
                'status' => 'approved',
                'parameters' => [
                    'Grabado' => [
                        ['name' => 'Velocidad', 'value' => '3500', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '22', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'LPI', 'value' => '250', 'unit' => 'lpi'],
                    ],
                    'Corte' => [
                        ['name' => 'Velocidad', 'value' => '350', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '90', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '3', 'unit' => 'unidad'],
                    ],
                ],
            ],
            [
                'title' => 'Grabado profundo MDF 3mm - Sellos',
                'material' => 'MDF',
                'job_type' => 'Grabado',
                'thickness_mm' => 3.0,
                'machine' => 'TwoTrees TS2',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Grabado profundo para hacer sellos de caucho sobre MDF.',
                'results' => 'Profundidad buena para sellos. Requiere varias pasadas.',
                'observations' => 'Hacer pasadas múltiples con enfriamiento entre cada una.',
                'status' => 'approved',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '2000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '40', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '4', 'unit' => 'unidad'],
                        ['name' => 'LPI', 'value' => '200', 'unit' => 'lpi'],
                    ],
                ],
            ],

            // Acrílico - varias experiencias
            [
                'title' => 'Corte acrílico transparente 3mm',
                'material' => 'Acrílico',
                'job_type' => 'Corte',
                'thickness_mm' => 3.0,
                'machine' => 'Sculpfun S9',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Corte de acrílico transparente (cast) 3mm para piezas decorativas.',
                'results' => 'Bordes pulidos por calor, corte limpio sin rebabas.',
                'observations' => 'Retirar film protector solo después del corte. Usar aire asistido fuerte.',
                'status' => 'approved',
                'parameters' => [
                    'Corte' => [
                        ['name' => 'Velocidad', 'value' => '800', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '70', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'Frecuencia', 'value' => '20', 'unit' => 'kHz'],
                    ],
                ],
            ],
            [
                'title' => 'Grabado acrílico espejo 2mm - Reverso',
                'material' => 'Acrílico',
                'job_type' => 'Grabado',
                'thickness_mm' => 2.0,
                'machine' => 'Ortur Laser Master 2',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Grabado por el reverso en acrílico espejo para señalética.',
                'results' => 'Grabado nítido, buena reflexión. Sin daños en capa reflectiva.',
                'observations' => 'Grabar en modo espejo (imagen invertida). Baja potencia para no dañar backing.',
                'status' => 'approved',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '5000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '15', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'DPI', 'value' => '400', 'unit' => 'dpi'],
                    ],
                ],
            ],
            [
                'title' => 'Corte acrílico negro 5mm',
                'material' => 'Acrílico',
                'job_type' => 'Corte',
                'thickness_mm' => 5.0,
                'machine' => 'Atomstack A10 Pro',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Corte de acrílico negro extruido 5mm.',
                'results' => 'Corte completo pero bordes algo fundidos. Mejor con más pasadas a menor potencia.',
                'observations' => 'Acrílico extruido funde más que cast. Probar 2 pasadas al 60%.',
                'status' => 'approved',
                'parameters' => [
                    'Corte' => [
                        ['name' => 'Velocidad', 'value' => '300', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '85', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '2', 'unit' => 'unidad'],
                    ],
                ],
            ],
            [
                'title' => 'Grabado foto acrílico blanco 4mm',
                'material' => 'Acrílico',
                'job_type' => 'Grabado',
                'thickness_mm' => 4.0,
                'machine' => 'TwoTrees TS2',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Grabado fotográfico en acrílico blanco opaco.',
                'results' => 'Buen contraste blanco/negro. Efecto "frosted" muy bonito.',
                'observations' => 'Sin asistencia de aire para mejor efecto frosting.',
                'status' => 'approved',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '2500', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '30', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'LPI', 'value' => '300', 'unit' => 'lpi'],
                    ],
                ],
            ],

            // Cuero - pocas experiencias para probar info insuficiente
            [
                'title' => 'Grabado logos en cuero vegetal 2mm',
                'material' => 'Cuero',
                'job_type' => 'Grabado',
                'thickness_mm' => 2.0,
                'machine' => 'Sculpfun S9',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Grabado de logotipos en cuero vegetal curtido al natural.',
                'results' => 'Grabado oscuro y permanente. Buen contraste natural.',
                'observations' => 'Probar en retazo primero. El cuero varía mucho según curtido.',
                'status' => 'approved',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '6000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '12', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'LPI', 'value' => '250', 'unit' => 'lpi'],
                    ],
                ],
            ],
            [
                'title' => 'Corte cuero 1.5mm - Patrones costura',
                'material' => 'Cuero',
                'job_type' => 'Corte',
                'thickness_mm' => 1.5,
                'machine' => 'Ortur Laser Master 2',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Corte de piezas para cartera artesanal.',
                'results' => 'Corte limpio, bordes sellados por calor. Sin deshilachado.',
                'observations' => 'Olor fuerte durante el corte. Ventilación obligatoria.',
                'status' => 'approved',
                'parameters' => [
                    'Corte' => [
                        ['name' => 'Velocidad', 'value' => '1500', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '50', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                    ],
                ],
            ],

            // Madera de pino - varias experiencias
            [
                'title' => 'Grabado artístico madera pino 5mm',
                'material' => 'Madera de pino',
                'job_type' => 'Grabado',
                'thickness_mm' => 5.0,
                'machine' => 'Sculpfun S9',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Grabado artístico con diferentes tonos aprovechando veta de la madera.',
                'results' => 'Efecto 3D muy bonito. La veta aporta textura natural.',
                'observations' => 'Lijar suavemente antes. La resina de la madera puede humear.',
                'status' => 'approved',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '3000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '35', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'LPI', 'value' => '200', 'unit' => 'lpi'],
                    ],
                    'Hatch 2 (sombras)' => [
                        ['name' => 'Velocidad', 'value' => '2000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '50', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'LPI', 'value' => '200', 'unit' => 'lpi'],
                    ],
                ],
            ],
            [
                'title' => 'Corte madera pino 6mm - Tablero',
                'material' => 'Madera de pino',
                'job_type' => 'Corte',
                'thickness_mm' => 6.0,
                'machine' => 'Atomstack A10 Pro',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Corte de tablero de pino 6mm para caja de madera.',
                'results' => 'Corte completo pero requiere 4 pasadas. Bordes carbonizados.',
                'observations' => 'Madera de pino con nudos corta irregular. Evitar nudos en línea de corte.',
                'status' => 'approved',
                'parameters' => [
                    'Corte' => [
                        ['name' => 'Velocidad', 'value' => '250', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '90', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '4', 'unit' => 'unidad'],
                    ],
                ],
            ],
            [
                'title' => 'Grabado nombre en madera pino 10mm - Tabla',
                'material' => 'Madera de pino',
                'job_type' => 'Grabado',
                'thickness_mm' => 10.0,
                'machine' => 'TwoTrees TS2',
                'machine_power_w' => 10,
                'software' => 'LaserGRBL',
                'description' => 'Grabado de nombre en tabla de cortar de pino 10mm.',
                'results' => 'Grabado superficial pero visible. Para uso alimentario sellar después.',
                'observations' => 'Madera gruesa no afecta al grabado superficial.',
                'status' => 'approved',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '4000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '25', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'DPI', 'value' => '300', 'unit' => 'dpi'],
                    ],
                ],
            ],

            // Cartón - pocas experiencias
            [
                'title' => 'Corte cartón corrugado 3mm - Packaging',
                'material' => 'Cartón',
                'job_type' => 'Corte',
                'thickness_mm' => 3.0,
                'machine' => 'Sculpfun S9',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Corte de plantillas para cajas de cartón corrugado.',
                'results' => 'Corte muy rápido y limpio. Ideal para prototipos packaging.',
                'observations' => 'Cartón prende fuego fácil. Velocidad alta, potencia baja, aire fuerte.',
                'status' => 'approved',
                'parameters' => [
                    'Corte' => [
                        ['name' => 'Velocidad', 'value' => '8000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '20', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                    ],
                ],
            ],
            [
                'title' => 'Grabado cartulina 300gsm - Tarjetas',
                'material' => 'Cartón',
                'job_type' => 'Grabado',
                'thickness_mm' => 0.3,
                'machine' => 'Ortur Laser Master 2',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Grabado fino en cartulina para tarjetas de visita.',
                'results' => 'Detalle excelente. Papel no se quema con estos parámetros.',
                'observations' => 'Fijar bien la cartulina para que no se mueva con el aire.',
                'status' => 'approved',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '10000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '8', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'DPI', 'value' => '600', 'unit' => 'dpi'],
                    ],
                ],
            ],

            // Vidrio - muy pocas experiencias
            [
                'title' => 'Grabado vaso de vidrio - Rotativo',
                'material' => 'Vidrio',
                'job_type' => 'Grabado',
                'thickness_mm' => null,
                'machine' => 'Sculpfun S9',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Grabado en vaso cilíndrico usando eje rotativo.',
                'results' => 'Efecto esmerilado elegante. Requiere eje rotativo bien calibrado.',
                'observations' => 'Poner toalla de papel mojada sobre zona a grabar para evitar microfisuras.',
                'status' => 'approved',
                'parameters' => [
                    'Rotativo' => [
                        ['name' => 'Velocidad', 'value' => '2000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '100', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'LPI', 'value' => '250', 'unit' => 'lpi'],
                    ],
                ],
            ],

            // Acero inoxidable (marcado) - muy pocas
            [
                'title' => 'Marcado acero inoxidable - Herramientas',
                'material' => 'Acero inoxidable (marcado)',
                'job_type' => 'Marcado',
                'thickness_mm' => null,
                'machine' => 'Atomstack A10 Pro',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Marcado negro en acero inoxidable (herramientas, cuchillos).',
                'results' => 'Marcado negro permanente, alto contraste. No es grabado profundo.',
                'observations' => 'Requiere pasta de marcado (CerMark o similar) o parámetros específicos sin pasta.',
                'status' => 'approved',
                'parameters' => [
                    'Marcado' => [
                        ['name' => 'Velocidad', 'value' => '500', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '100', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '2', 'unit' => 'unidad'],
                        ['name' => 'LPI', 'value' => '400', 'unit' => 'lpi'],
                        ['name' => 'Desenfoque', 'value' => '2', 'unit' => 'mm'],
                    ],
                ],
            ],

            // Goma EVA - pocas
            [
                'title' => 'Corte goma EVA 2mm - Sellos infantiles',
                'material' => 'Goma EVA',
                'job_type' => 'Corte',
                'thickness_mm' => 2.0,
                'machine' => 'TwoTrees TS2',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Corte de formas para sellos infantiles en goma EVA.',
                'results' => 'Corte limpio, bordes suaves. Material muy fácil de cortar.',
                'observations' => 'Olor a plástico quemado. Ventilación buena. No usar potencia alta.',
                'status' => 'approved',
                'parameters' => [
                    'Corte' => [
                        ['name' => 'Velocidad', 'value' => '3000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '30', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                    ],
                ],
            ],
            [
                'title' => 'Grabado goma EVA 3mm - Texturas',
                'material' => 'Goma EVA',
                'job_type' => 'Grabado',
                'thickness_mm' => 3.0,
                'machine' => 'Sculpfun S9',
                'machine_power_w' => 10,
                'software' => 'LaserGRBL',
                'description' => 'Grabado de texturas para manualidades en goma EVA.',
                'results' => 'Textura bien marcada. Material suave, graba muy rápido.',
                'observations' => 'Muy sensible al calor. Velocidad alta obligatoria.',
                'status' => 'approved',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '8000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '10', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'DPI', 'value' => '300', 'unit' => 'dpi'],
                    ],
                ],
            ],

            // Bambú - varias
            [
                'title' => 'Grabado tabla bambú - Cocina',
                'material' => 'Bambú',
                'job_type' => 'Grabado',
                'thickness_mm' => 12.0,
                'machine' => 'Sculpfun S9',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Grabado personalizado en tabla de cortar de bambú.',
                'results' => 'Grabado claro y duradero. Bambú denso aguanta bien el calor.',
                'observations' => 'Aceitar tabla después del grabado para protección alimentaria.',
                'status' => 'approved',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '3000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '40', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'LPI', 'value' => '250', 'unit' => 'lpi'],
                    ],
                ],
            ],
            [
                'title' => 'Corte bambú 4mm - Lámparas',
                'material' => 'Bambú',
                'job_type' => 'Corte',
                'thickness_mm' => 4.0,
                'machine' => 'Ortur Laser Master 2',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Corte de piezas para lámparas de bambú.',
                'results' => 'Corte limpio, veta bonita en bordes. Poco carbonizado.',
                'observations' => 'Bambú más uniforme que madera. Menos problemas con nudos.',
                'status' => 'approved',
                'parameters' => [
                    'Corte' => [
                        ['name' => 'Velocidad', 'value' => '500', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '80', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '2', 'unit' => 'unidad'],
                    ],
                ],
            ],
            [
                'title' => 'Grabado foto bambú 5mm',
                'material' => 'Bambú',
                'job_type' => 'Grabado',
                'thickness_mm' => 5.0,
                'machine' => 'Atomstack A10 Pro',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Grabado fotográfico en lámina de bambú prensado.',
                'results' => 'Buen detalle. Tono dorado natural del bambú da calidez a la imagen.',
                'observations' => 'LPI alto para fotos. Probar 300-400 LPI.',
                'status' => 'approved',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '2500', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '35', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'LPI', 'value' => '350', 'unit' => 'lpi'],
                    ],
                ],
            ],

            // Anodizado de aluminio - varias
            [
                'title' => 'Marcado anodizado aluminio - Placas identificación',
                'material' => 'Anodizado de aluminio',
                'job_type' => 'Marcado',
                'thickness_mm' => 1.5,
                'machine' => 'Sculpfun S9',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Marcado en blanco sobre anodizado negro para placas industriales.',
                'results' => 'Contraste excelente blanco/negro. Muy legible y duradero.',
                'observations' => 'El láser quita la capa anodizada revelando el aluminio brillante.',
                'status' => 'approved',
                'parameters' => [
                    'Marcado' => [
                        ['name' => 'Velocidad', 'value' => '8000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '60', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'LPI', 'value' => '400', 'unit' => 'lpi'],
                    ],
                ],
            ],
            [
                'title' => 'Grabado logo anodizado rojo - Llaveros',
                'material' => 'Anodizado de aluminio',
                'job_type' => 'Grabado',
                'thickness_mm' => 2.0,
                'machine' => 'TwoTrees TS2',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Grabado de logotipo en llaveros de aluminio anodizado rojo.',
                'results' => 'Logo plateado brillante sobre fondo rojo. Muy vistoso.',
                'observations' => 'Funciona igual en cualquier color de anodizado.',
                'status' => 'approved',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '10000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '50', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'DPI', 'value' => '500', 'unit' => 'dpi'],
                    ],
                ],
            ],
            [
                'title' => 'Marcado serie anodizado - Control calidad',
                'material' => 'Anodizado de aluminio',
                'job_type' => 'Marcado',
                'thickness_mm' => 3.0,
                'machine' => 'Ortur Laser Master 2',
                'machine_power_w' => 10,
                'software' => 'LaserGRBL',
                'description' => 'Marcado de números de serie en piezas anodizadas.',
                'results' => 'Marcado rápido y permanente. Ideal para trazabilidad industrial.',
                'observations' => 'Configurar contador automático en LightBurn para series.',
                'status' => 'approved',
                'parameters' => [
                    'Marcado' => [
                        ['name' => 'Velocidad', 'value' => '12000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '40', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '1', 'unit' => 'unidad'],
                        ['name' => 'DPI', 'value' => '600', 'unit' => 'dpi'],
                    ],
                ],
            ],

            // Tela - SIN experiencias (para probar material sin casos)
            // No agregamos ninguna experiencia de tela

            // Experiencias pendientes y rechazadas para probar filtros
            [
                'title' => 'Prueba MDF 3mm - Pendiente revisión',
                'material' => 'MDF',
                'job_type' => 'Grabado',
                'thickness_mm' => 3.0,
                'machine' => 'Sculpfun S9',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Experiencia pendiente de aprobación.',
                'results' => 'Resultado preliminar.',
                'observations' => 'Esperando revisión moderador.',
                'status' => 'pending',
                'parameters' => [
                    'Hatch 1' => [
                        ['name' => 'Velocidad', 'value' => '3000', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '25', 'unit' => '%'],
                    ],
                ],
            ],
            [
                'title' => 'Fallido - Acrílico 10mm corte',
                'material' => 'Acrílico',
                'job_type' => 'Corte',
                'thickness_mm' => 10.0,
                'machine' => 'Sculpfun S9',
                'machine_power_w' => 10,
                'software' => 'LightBurn',
                'description' => 'Intento de corte acrílico 10mm con 10W - No corta.',
                'results' => 'No atraviesa. Máxima potencia y 10 pasadas solo marca.',
                'observations' => 'Rechazada: parámetros no funcionan para este espesor con 10W. Necesita más potencia.',
                'status' => 'rejected',
                'parameters' => [
                    'Corte' => [
                        ['name' => 'Velocidad', 'value' => '100', 'unit' => 'mm/min'],
                        ['name' => 'Potencia', 'value' => '100', 'unit' => '%'],
                        ['name' => 'Pasadas', 'value' => '10', 'unit' => 'unidad'],
                    ],
                ],
            ],
        ];

        foreach ($experiences as $expData) {
            $params = $expData['parameters'] ?? [];
            unset($expData['parameters']);

            $experience = Experience::create([
                'title' => $expData['title'],
                'material_id' => $materials[$expData['material']]->id,
                'job_type_id' => $jobTypes[$expData['job_type']]->id,
                'thickness_mm' => $expData['thickness_mm'],
                'machine' => $expData['machine'],
                'machine_power_w' => $expData['machine_power_w'],
                'software' => $expData['software'],
                'description' => $expData['description'],
                'results' => $expData['results'],
                'observations' => $expData['observations'],
                'status' => $expData['status'],
            ]);

            foreach ($params as $blockName => $blockParams) {
                foreach ($blockParams as $param) {
                    ExperienceParameter::create([
                        'experience_id' => $experience->id,
                        'block_name' => $blockName,
                        'name' => $param['name'],
                        'value' => $param['value'],
                        'unit' => $param['unit'] ?: null,
                    ]);
                }
            }
        }
    }
}