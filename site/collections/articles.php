<?php

return fn () => page('blog')->children()->listed()->sortBy('date', 'desc');
