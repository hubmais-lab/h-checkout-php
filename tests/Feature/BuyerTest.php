<?php

beforeEach(function(){    
    $this->api = init_api();    
});

test('Salvar dados Cliente', function(){
    
    $response = $this->api->buyers()->save(
        '157.937.650-93',
        'Fulano de Tal',
        '1998-08-16',
        'fulanodetal@gmail.com',
        '(11) 99987-0023',
        [
            'postal_code' => '01310000',
            'street' => 'Av. Paulista',
            'number' => '14500',
            'neighborhood' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP'
        ]
    );

    expect($response !== null)->toBeTrue();
});


