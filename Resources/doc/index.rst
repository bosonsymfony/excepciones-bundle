Componente: ExcepcionesBundle
=============================


1. Descripción general
----------------------

    Está orientado a gestionar las excepciones locales de cada bundle.
    Garantiza también el registro de todas las excepciones lanzadas en tiempo de ejecución en ficheros logs con un formato entendible para su posterior análisis.


2. Instalación
--------------

    1. Copiar el componente dentro de la carpeta `vendor/boson/excepciones-bundle/UCI/Boson`.
    2. Registrarlo en el archivo `app/autoload.php` de la siguiente forma:

       .. code-block:: php

           // ...
           $loader = require __DIR__ . '/../vendor/autoload.php';
           $loader->add("UCI\\Boson\\ExcepcionesBundle", __DIR__ . '/../vendor/boson/excepciones-bundle');
           // ...

    3. Activarlo en el kernel de la siguiente manera:

       .. code-block:: php

           // app/AppKernel.php
           public function registerBundles()
           {
               return array(
                   // ...
                   new UCI\Boson\ExcepcionesBundle\ExcepcionesBundle(),
                   // ...
               );
           }

    4. Luego debes modificar la Clase DependencyInjection\[Nombredetubundle]Extension.php específicamente el método **load**
       para que cargue las excepciones de tu bundle:

	   .. code-block:: php

	   	   //clase AcmeExtension, método load
		   public function load()
		   {
		       // ...
			   $loader->load('services.yml');

			   $ExcpExtension = new ExcepcionesExtension();
			   $ExcpExtension->loadFileExcepciones($container,__DIR__);
		   }

3. Especificación funcional
---------------------------

3.1. Requisitos funcionales
~~~~~~~~~~~~~~~~~~~~~~~~~~~


3.1.1. Configuración y registro de las distintas excepciones que pueden ocurrir en cada acción de las clases control que se implementan.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

	Los ficheros de configuración deben encontrarse en la dirección MyBundle/Resources/config/ y 		deben tener el 		nombre excepciones.yml o excepciones.xml según el tipo de 		configuración que se desee.
	Si el tipo de fichero es de tipo YAML el fichero debe estar configurado de la siguiente forma:

	.. code-block:: php

	    excepciones:
	        .....

	Si el tipo de fichero es de tipo XML el fichero debe estar configurado de la siguiente forma:

	.. code-block:: xml

	    <?xml version="1.0" encoding="UTF-8"?>
		<excepciones>
        			....
		</excepciones>


3.1.2. Configuración y registro de los datos de las excepciones que se adicionan.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

	Las excepciones registradas en los ficheros de configuración deben poseer un código que debe ser la identificación de la 			excepción. Además del código debe especificarse un mensaje y una descripción.

	Si el tipo de fichero es de tipo YAML  un ejemplo de excepción  con código E1 debería quedar de la siguiente forma:

	.. code-block:: php

	    excepciones:
		    E1:
		        mensaje: excepciones.E605.mensaje
		        descripcion: este es la descripción

	Si el tipo de fichero es de tipo XML el fichero debe estar configurado de la siguiente forma:

	.. code-block:: xml

	    <?xml version="1.0" encoding="UTF-8"?>
		<excepciones>
        	<E1>
		        <mensaje>
		            acmejuju.excepciones.E1.mensaje
		        </mensaje>
		        <descripcion>
		            Descripción de la excepción de ejemplo.
		        </descripcion>
		    </E1>
		</excepciones>

3.1.3. Realizar el tratamiento de excepciones.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

    Las excepciones se tratan siguiendo los métodos convencionales del lenguaje php, para el lanzamiento de las excepciones locales configuradas en su componente, se debe agregar el código de la excepción configurada en el archivo excepciones.*, como segundo parámetro opcionalmente puede ser pasado  una excepción previamente capturada y que sea de interés para el programa, siguiendo la misma lógica definida para todas las excepciones de php.

		.. code-block:: php

		    throw new LocalException("E1") //para lanzarlas


    El componente cuenta con la personalización de las vistas de errores para el entorno de producción, usted puede alterar los mismos o crear nuevos siempre que especifique una plantilla con el nombre 'error'+código.html.twig en la carpeta Resources/views/Exception/.
    Siéntase libre de modificar y personalizar estas plantillas a su gusto (ver libro **The Cookbook of Symfony2.3** para mayor información).
    Para el uso de las plantillas se especifica en app/config/parameters_boson.yml el parámetro **excepciones.email_admin_contact** el cual debe indicar la dirección de correo del administrador como contacto para los errores.

3.1.4. Obtener el identificador o código de la excepción.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	El identificador o código de toda excepción de tipo **LocalException** puede ser obtenido mediante el 	método getCodigo()  o mediante el método nativo de las excepciones genéricas de php getCode().

3.1.5. Obtener la excepción interna que ocurrió.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	La excepción interna de toda excepción de tipo **LocalException** puede ser obtenido mediante el método getInterna()  o mediante el método nativo de las excepciones genéricas de php getPrevious().

3.1.6. Obtener la descripción de la excepción.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	La descripción de toda excepción de tipo **LocalException** puede ser obtenida mediante el método getDescripcion().

3.1.7. Obtener la clase que lanzó la excepción.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	La clase de toda excepción de tipo **LocalException** puede ser obtenido mediante el método getClase() o mediante el método nativo de las excepciones genéricas de php getFile().

3.1.8. Obtener la acción de la clase que disparó la excepción.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	La acción de la clase de toda excepción de tipo **LocalException** puede ser obtenido mediante el método getMetodo().

3.1.9. Obtener el bundle en el que ocurrió la excepción.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	El nombre del bundle en el que ocurrió la excepción de toda excepción de tipo **LocalException** puede ser obtenido mediante el método getBundleName().

3.1.10. Obtener el mensaje de la excepción.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	El mensaje de la excepción que ocurrió de toda excepción de tipo **LocalException** puede ser obtenido mediante el método getMensaje() o mediante el método nativo de las excepciones genéricas de php getMessage().

3.1.11. Salvar y mostrar los datos de la excepción.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	El bundle registra todas las excepciones lanzadas, ya sea las te tipo LocalExcepcion como cualquier otra excepción lanzada durante el tiempo de ejecución de la aplicación.
	Las excepciones son registradas en el fichero excepciones.log que puede ser encontrado en  app/logs. Por cada excepción lanzada se registran en un formato entendible los principales datos de la misma asi como la fecha y hora en que fue lanzada. Este registro se realiza valiéndose del bundle monolog.

3.1.12. Personalizar comando de generar bundles
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	El bundle brinda el comando boson:generate:bundle el cual es una modificación del generate:bundle de sensio. Este comando permite generar un nuevo bundle con la estructura de carpetas y ficheros necesarios para empezar la implementación y uso de las excepciones locales sin necesidad de incluir.

3.2. Requisitos no funcionales
------------------------------
	La **internacionalización**  de los mensajes y las descripciones de las excepciones pueden ser configuradas con facilidad. Basta con sustituir el texto de estos por el código necesario para acceder a los mensajes descritos en los ficheros de internacionalización ubicados en el bundle en Resources/translations/translatesexcepciones.[código del idioma].[yml o xrtf], un ejemplo pudiera ser el siguiente:
		.. code-block:: php

		    # fichero ../Resources/config/excepciones.yml
	    	    excepciones:
                  E605:
			          mensaje: excepciones.E605.mensaje
			          descripcion: este es la descripción
			          ..........

		.. code-block:: php

		    # fichero ../Resources/translations/translatesexcepciones.es.yml
	    	    excepciones:
		        E1:
		            mensaje: excepciones.E1.mensaje
		            descripcion: este es la descripción


4. Servicios que brinda
-----------------------


5. Servicios de los que depende
-------------------------------
	Listado de servicios que constituyen dependencias del bundle:

    - translator

6. Eventos generados
--------------------

7. Eventos observados
---------------------

	.. code-block:: php

	    onKernelException(GetResponseForExceptionEvent $event)

	El evento onKernelException es observado con el objetivo de escribir los logs de las excepciones ocurridas en el sistema. Ver implementación  de la clase ..\\ExcepcionesBundle\\EventListener.

---------------------------------------------

:Versión: 1.0 17/7/2015
:Autores: Daniel Arturo Casals Amat dacasals@uci.cu

Contribuidores
--------------

:Entidad: Universidad de las Ciencias Informáticas. Centro de Informatización de Entidades.

Licencia
--------
