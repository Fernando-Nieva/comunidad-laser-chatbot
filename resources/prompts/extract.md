Extrae los datos relevantes de la conversación para buscar parámetros de grabado láser.

DEVOLVÉ SOLO UN JSON VÁLIDO con estos campos (usá null si no se mencionó):
{
  "material": "string|null",
  "thickness_mm": "number|null",
  "machine_power_w": "number|null",
  "job_type": "string|null",
  "machine": "string|null",
  "software": "string|null"
}

REGLAS:
- material: nombre del material (ej: "MDF", "acrílico", "madera de pino", "cuero")
- thickness_mm: espesor en mm (ej: 3, 5.5)
- machine_power_w: potencia del láser en vatios (ej: 10, 20, 40)
- job_type: "grabado", "corte", "marcado", "grabado y corte"
- machine: modelo de máquina (ej: "Sculpfun S9", "Ortur LM2")
- software: software usado (ej: "LightBurn", "LaserGRBL")

NO agregues texto extra, NO uses markdown, SOLO el JSON.