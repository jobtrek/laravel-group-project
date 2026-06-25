@props([
    'gerent_name' => 'Jac',
    'estimate_date' => '3 mois',
    'finish_date' => '19/09/2026',
    'importance_details' => 'Medium'
])

<div class="flex justify-between mt-1 text-sm text-gray-600"><p>Gerant du projet :</p><span>{{ $gerent_name }}</span></div>
<div class="flex justify-between mt-1 text-sm text-gray-600"><p>Temps estimée : <p/><span>{{$estimate_date}}</span></div>
<div class="flex justify-between mt-1 text-sm text-gray-600"><p>Date de finition :<p/><span>{{$finish_date}}</span></div>
<div class="flex justify-between mt-1 text-sm text-gray-600"><p>Importance :<p/><span>{{$importance_details}}</span></div>
