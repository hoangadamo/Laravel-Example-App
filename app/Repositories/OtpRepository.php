<?php

namespace App\Repositories;

use App\Models\OTP;

class OtpRepository
{
    protected $otp;
    public function __construct(OTP $otp)
    {
        $this->otp = $otp;
    }
    public function create($data)
    {
        return $this->otp->create($data);
    }

    public function get()
    {
        return $this->otp->get();
    }

    public function getById($id)
    {
        return $this->otp->where('id', $id)->first();
    }

    public function update($id, $data)
    {
        return $this->otp->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        $this->otp->where('id', $id)->delete();
    }

    public function paginate($limit)
    {
        return $this->otp->paginate($limit);
    }

    public function getByOtp($otp)
    {
        return $this->otp->where('otp', $otp)->first();
    }
}
