<p align="center">Yet Another Cash Flow Control</p>


## About Yet Another Cash Flow Control

Yet Another Cash Flow Control (YACFC) is a web application focuses on solve complex financial problems for personal use, their main goals are:

- Registry of single, recursive and many kinds of incomes and expenses


## ToDo
- Check the balance of every account
- Check the expenses of credit cards

-Habilitar ABM manual de operaciones dentro de un plan ??
-Boton para cerrar plan... elimina las operaciones pendientes (no concretadas y marca el plan como completo)


-Vistas
-Opciones generales para todas las vistas
    *Año
    *Mes
    *Egresos / Ingresos / Pasivo / Movimiento / Ajuste (Selección Multiple)
    *Pendientes / realizados (Selección Multiple)

-CashFlow
-Agrupar por Cuenta
-Agrupar por Categoría

Columnas
-Fecha estimada
-Concepto de entrada
-Detalle de operación ?
-Cuenta
-Monto estimado
-Monto concretado
-Fecha de concretado
-Botón de editar
-Boton de concretar
-Boton para ver todas las operaciones de esa entrada

Popup con toda la info restante en un boton ver


En caso de diferencias con el sistema de tarjetas de crédito (porque no incluyó una operación y la pasó para el período siguiente) podemos cambiar la fecha estimada de operacion / fecha de operación para pasarla al período siguiente

Para soportar las tarjetas de crédito el sistema registra fecha de inicio de periodo y fecha de fin (mensualmente) (diferente de la fecha de vencimiento que registramos como fecha estimada), 
Al realizar un compra registramos en el sistema un pasivo usando la tarjeta de crédito como cuenta, dicho pasivo marcará todas las operaciones como concretado inmediatamente (aunque sean 12 o más cuotas) ya que la deuda ya fue contraida en el momento de la compra
A su vez, el sistema dispondrá de un egreso por mes para dicha tarjeta, cuyo monto será la suma de todas las operaciones de dicha tarjeta durante la vigencia del período más arriba mencionado.


Opciones de formato de detalle (sólo disponible para recurrente):
-Pago único  (automático para pago único)
-cuota numero/total (CUOTA 01/06) (automático para pago en cuotas)

Para recurrentes disponer estas opciones:
-desde dia/mes hasta dia/mes (período desde ... hasta ...) 
-cuota anualizada (CUOTA 01/06 - 2020) 
-mes/año - mes/año (ENERO 2020) 


Al cargar planes recurrentes o en cuotas el sistema generará automáticamente todas las operaciones necesarias con un tope máximo de 12/24 meses a definir, 



## License

The YACFC is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
