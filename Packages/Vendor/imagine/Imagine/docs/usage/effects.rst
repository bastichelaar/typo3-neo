Image effects
=============

Imagine also provides a fully-featured effects API.
To use the api, you need to get an effect instance from you current image
instance, using ``ImageInterface::effects()`` method.

Example
-------

.. code-block:: php

    <?php

    $image = $imagine->open('portrait.jpeg');

    $image->effects()
        ->negative()
        ->gamma(1.3);

    $image->save('negative-portrait.png');

The above example would open a "portrait.jpeg" image, invert the colors, then
corrects the gamma with a parameter of 1.3 then saves it to a new file
"negative-portrait.png".

.. NOTE::
    As you can notice, all effects are chainable.

Effects API
-----------

The current Effects API currently supports these effects :

Negative
++++++++

The negative effect inverts the color of an image :

.. code-block:: php

    <?php

    $image = $imagine->open('portrait.jpeg');

    $image->effects()
        ->negative();

    $image->save('negative-portrait.png');

Gamma correction
++++++++++++++++

Apply a gamma correction. It takes one float argument, the correction parameter.

.. code-block:: php

    <?php

    $image = $imagine->open('portrait.jpeg');

    $image->effects()
        ->gamma(0.7);

    $image->save('negative-portrait.png');

