# Imágenes de escenarios para Batalla Naval

Esta carpeta contiene las imágenes de fondo para los diferentes escenarios del juego.

## Estructura de archivos

- `classic.png`: Fondo para el escenario clásico (mar tranquilo, alta visibilidad)
- `foggy.png`: Fondo para el escenario de niebla (visibilidad reducida)
- `stormy.png`: Fondo para el escenario de tormenta (mares agitados)
- `rain-overlay.png`: Textura de lluvia para efectos adicionales en el escenario de tormenta

## Recomendaciones para las imágenes

### Formato PNG
Estamos usando imágenes PNG por las siguientes ventajas:
- Soporte para transparencia
- Mejor calidad para imágenes con detalles finos
- Sin pérdida de calidad por compresión

### Especificaciones técnicas
- Resolución recomendada: 1920x1080px para fondos principales
- Para rain-overlay.png: utilizar una textura semitransparente más pequeña que se pueda repetir (patrón)
- Profundidad de color: 24-bit o 32-bit con canal alfa (para transparencia)
- Optimizar las imágenes para web para reducir el tiempo de carga

## Descripciones de escenarios

### Escenario Clásico (classic.png)
- Imagen de mar tranquilo, preferiblemente con tonos azules
- Alta visibilidad, aguas claras
- Condiciones óptimas para la batalla naval

### Escenario de Niebla (foggy.png)
- Imagen de mar con niebla espesa, tonos grisáceos o blanquecinos
- La niebla debe ser prominente para reflejar la baja visibilidad del escenario
- Atmósfera misteriosa y limitada visibilidad

### Escenario de Tormenta (stormy.png)
- Imagen dramática de mar tormentoso con olas grandes
- Puede incluir relámpagos, nubes oscuras o lluvia
- Tonos más oscuros (azul marino, gris oscuro)
- Sensación de peligro y desafío

### Textura de lluvia (rain-overlay.png)
- Patrón semitransparente de gotas o líneas de lluvia
- Tamaño pequeño que se pueda repetir (tileable)
- Preferiblemente con fondo transparente

## Consideraciones técnicas

- Las imágenes se utilizan como fondos del panel de juego, con un overlay oscuro encima
- Deben tener buen contraste para que se vean los elementos del juego
- Evitar imágenes con patrones muy marcados que puedan distraer la vista
