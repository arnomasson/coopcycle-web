<?php

namespace AppBundle\Entity;

class DayOfWeekAwareConfig
{
    private $id;

    private $restaurant;

    private $daysOfWeek;

    private $address;

    private $openingHours;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRestaurantId()
    {
        return $this->restaurant_id;
    }

    /**
     * @param mixed $restaurant_id
     *
     * @return self
     */
    public function setRestaurantId($restaurant_id)
    {
        $this->restaurant_id = $restaurant_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDaysOfWeek()
    {
        return $this->days_of_week;
    }

    /**
     * @param mixed $days_of_week
     *
     * @return self
     */
    public function setDaysOfWeek($days_of_week)
    {
        $this->days_of_week = $days_of_week;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddressId()
    {
        return $this->address_id;
    }

    /**
     * @param mixed $address_id
     *
     * @return self
     */
    public function setAddressId($address_id)
    {
        $this->address_id = $address_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOpeningHours()
    {
        return $this->opening_hours;
    }

    /**
     * @param mixed $opening_hours
     *
     * @return self
     */
    public function setOpeningHours($opening_hours)
    {
        $this->opening_hours = $opening_hours;

        return $this;
    }
}
