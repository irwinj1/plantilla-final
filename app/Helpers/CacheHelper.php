<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
  /**
     * Obtiene datos desde cache (Redis/Mongo) o ejecuta la consulta y la guarda.
     *
     * @param string $key Clave única del cache
     * @param int $seconds Tiempo de vida del cache en segundos
     * @param \Closure $callback Consulta que debe ejecutarse si no hay cache
     * @return mixed
     */
    public static function remember(string $key, int $second, \Closure $callback){
      return Cache::remember($key, $second, $callback);
    }

     /**
     * Borra una clave específica del cache
     */
    public static function forget(string $key)
    {
        Cache::forget($key);
    }
}