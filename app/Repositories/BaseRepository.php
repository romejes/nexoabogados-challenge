<?php

namespace App\Repositories;

class BaseRepository
{
    /**
     * Instancia de un modelo Eloquent
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Nombre de la llave primaria del modelo
     *
     * @var string
     */
    protected $primaryKey;

    /**
     * Constructor
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct($model)
    {
        $this->model = $model;
        $this->primaryKey = $this->model->getKeyName();
    }

    /**
     * Obtiene todos los registros
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->get();
    }

    /**
     * Busca un registro mediante su ID. Si no encuentra un registro devolvera NULL.
     *
     * @param int $id
     * @return mixed
     */
    public function getByID($id)
    {
        return $this->model->where($this->primaryKey, $id)->first();
    }

    /**
     * Inserta un registro en la base de datos
     *
     * @param array $values
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function insert($values)
    {
        return $this->model->create($values);
    }

    /**
     * Actualiza un registro en la base de datos
     *
     * @param array $attributes
     * @param integer $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($values, $id)
    {
        $this->model->where($this->primaryKey, $id)->update($values);
        return $this->getByID($id);
    }
}

