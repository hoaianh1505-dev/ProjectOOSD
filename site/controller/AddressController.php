<?php
class AddressController
{
    function getDistricts()
    {
        $province_id = $_GET['province_id'];
        $districtsRepository = new DistrictRepository();
        $districts = $districtsRepository->getByProvinceId($province_id);
        // covert to json
        echo json_encode($districts);
    }

    function getWards()
    {
        $district_id = $_GET['district_id'];
        $wardRepository = new WardRepository();
        $wards = $wardRepository->getByDistrictId($district_id);
        // covert to json
        echo json_encode($wards);
    }

    function getShippingFee()
    {
        $province_id = $_GET['province_id'];
        $provinceRepository = new ProvinceRepository;
        $province = $provinceRepository->find($province_id);
        $shippingFee = $province->getShippingFee();
        echo $shippingFee;
    }
}
