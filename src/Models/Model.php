<?php
namespace Viauco\Base\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Notifications\Notifiable;
/**
 * Class     Model
 *
 * @package  Viauco\Survey\Models
 */
abstract class Model extends Eloquent
{
    use Notifiable;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    protected static function keyName()
    {
        return (new static)->getKeyName();
    }

    protected static function tableName()
    {
        return (new static)->getTable();
    }
}
