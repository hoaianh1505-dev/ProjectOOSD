<?php
// Danh sách provinces
$provinceRepository = new ProvinceRepository();
$provinces = $provinceRepository->getAll();

$selectedWard = $customer->getWard();
$districts = [];
$ward = [];
$selected_province_id = '';
if ($selectedWard) {
    $selectedDistrict = $selectedWard->getDistrict();
    $selectedProvince = $selectedDistrict->getProvince();

    $districts = $selectedProvince->getDistricts();
    $wards = $selectedDistrict->getWards();

    $selected_province_id = $selectedProvince->getId();
    $selected_district_id = $selectedDistrict->getId();
    $selected_ward_id = $selectedWard->getId();
}
