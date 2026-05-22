<?php

/**
 * Dados fictícios para demo de relatórios — Distinção Empresarial, Bagé-RS.
 * Estabelecimentos com peso (mais votos) e variações de escrita para testar classificação.
 */
return [
    'cidade' => 'Bagé',
    'estado' => 'RS',

    'profissoes' => [
        'Comerciante', 'Autônomo', 'Estudante', 'Aposentado', 'Empresário',
        'Profissional liberal', 'Servidor público', 'Produtor rural', 'Técnico',
        'Administrador', 'Vendedor', 'Professor', 'Enfermeiro', 'Contador',
    ],

    'bairros' => [
        ['bairro' => 'Centro', 'rua' => 'Rua General Osório', 'lat' => -31.3312, 'lng' => -54.1068],
        ['bairro' => 'Getúlio Vargas', 'rua' => 'Av. Sete de Setembro', 'lat' => -31.3285, 'lng' => -54.1121],
        ['bairro' => 'Boné Azul', 'rua' => 'Rua Coronel João Manoel', 'lat' => -31.3358, 'lng' => -54.0985],
        ['bairro' => 'Medianeira', 'rua' => 'Rua Medianeira', 'lat' => -31.3264, 'lng' => -54.1153],
        ['bairro' => 'Prado Velho', 'rua' => 'Rua Prado Velho', 'lat' => -31.3391, 'lng' => -54.1042],
        ['bairro' => 'São João', 'rua' => 'Rua São João', 'lat' => -31.3247, 'lng' => -54.1089],
        ['bairro' => 'São Jorge', 'rua' => 'Av. Vasco da Gama', 'lat' => -31.3376, 'lng' => -54.1115],
        ['bairro' => 'Cidade Nova', 'rua' => 'Rua Cidade Nova', 'lat' => -31.3228, 'lng' => -54.1024],
        ['bairro' => 'Industrial', 'rua' => 'Rua Industrial', 'lat' => -31.3415, 'lng' => -54.1187],
        ['bairro' => 'Santo Antônio', 'rua' => 'Rua Santo Antônio', 'lat' => -31.3298, 'lng' => -54.0998],
        ['bairro' => 'Correntes', 'rua' => 'Rua Correntes', 'lat' => -31.3334, 'lng' => -54.1146],
        ['bairro' => 'Cecília', 'rua' => 'Rua Cecília', 'lat' => -31.3189, 'lng' => -54.1055],
        ['bairro' => 'Erro', 'rua' => 'Rua Erro', 'lat' => -31.3452, 'lng' => -54.1093],
        ['bairro' => 'Porto Seco', 'rua' => 'Av. Porto Seco', 'lat' => -31.3165, 'lng' => -54.1138],
        ['bairro' => 'Vila Nova', 'rua' => 'Rua Vila Nova', 'lat' => -31.3408, 'lng' => -54.0967],
        ['bairro' => 'São Francisco', 'rua' => 'Rua São Francisco', 'lat' => -31.3271, 'lng' => -54.1012],
        ['bairro' => 'Nossa Senhora Aparecida', 'rua' => 'Rua NS Aparecida', 'lat' => -31.3325, 'lng' => -54.1201],
        ['bairro' => 'Internacional', 'rua' => 'Rua Internacional', 'lat' => -31.3198, 'lng' => -54.0976],
        ['bairro' => 'Trevo', 'rua' => 'BR-293 km 0', 'lat' => -31.3481, 'lng' => -54.1058],
        ['bairro' => 'Expedicionário', 'rua' => 'Rua Expedicionário', 'lat' => -31.3255, 'lng' => -54.1169],
    ],

    /**
     * Segmento (nome exato da planilha) => candidatos com peso e variações opcionais.
     *
     * @var array<string, list<array{nome: string, peso: int, variacoes?: list<string>}>>
     */
    'estabelecimentos' => [
        'Floricultura' => [
            ['nome' => 'Floricultura Jade', 'peso' => 38, 'variacoes' => ['floricul jade', 'Floricultura  Jade', 'FLORICULTURA JADE', 'Floricultura jade', 'Floricultura JADE']],
            ['nome' => 'Floricultura Primavera', 'peso' => 22, 'variacoes' => ['Floricultura Primavera Bagé', 'floricultura primavera']],
            ['nome' => 'Floricultura do Centro', 'peso' => 14],
            ['nome' => 'Flores & Cia', 'peso' => 10],
            ['nome' => 'Jardim Encantado', 'peso' => 8],
        ],
        'Farmácia comercial' => [
            ['nome' => 'Farmácia São João', 'peso' => 32, 'variacoes' => ['Farmacia Sao Joao', 'FARMÁCIA SÃO JOÃO', 'farmácia são joão']],
            ['nome' => 'Panvel', 'peso' => 28, 'variacoes' => ['panvel', 'PANVEL Bagé']],
            ['nome' => 'Farmácia Central', 'peso' => 18],
            ['nome' => 'Drogaria Bagé', 'peso' => 12],
        ],
        'Supermercado' => [
            ['nome' => 'Supermercado Bagé', 'peso' => 35],
            ['nome' => 'Compre Bem', 'peso' => 28, 'variacoes' => ['Compre bem', 'COMPRE BEM']],
            ['nome' => 'Mercado do Bairro', 'peso' => 20],
            ['nome' => 'Atacadão Bagé', 'peso' => 15],
        ],
        'Restaurante' => [
            ['nome' => 'Restaurante Gaúcho', 'peso' => 30],
            ['nome' => 'Sabor da Fronteira', 'peso' => 26, 'variacoes' => ['Sabor da fronteira', 'sabor da fronteira']],
            ['nome' => 'Cantina do Centro', 'peso' => 22],
            ['nome' => 'Churrascaria Boi na Brasa', 'peso' => 18],
        ],
        'Pizzaria' => [
            ['nome' => 'Pizzaria Napolitana', 'peso' => 34],
            ['nome' => 'Pizza & Cia Bagé', 'peso' => 24],
            ['nome' => 'Forno de Pedra', 'peso' => 20],
        ],
        'Bancos' => [
            ['nome' => 'Banco do Brasil', 'peso' => 30],
            ['nome' => 'Sicredi', 'peso' => 28],
            ['nome' => 'Caixa Econômica', 'peso' => 22],
            ['nome' => 'Bradesco', 'peso' => 18],
        ],
        'Barbearia' => [
            ['nome' => 'Barbearia do Zé', 'peso' => 32, 'variacoes' => ['Barbearia do Ze', 'barbearia do zé']],
            ['nome' => 'Barber Shop Bagé', 'peso' => 24],
            ['nome' => 'Corte & Estilo', 'peso' => 20],
        ],
        'Academia de ginástica' => [
            ['nome' => 'Academia Fitness Bagé', 'peso' => 30],
            ['nome' => 'Body Gym', 'peso' => 26],
            ['nome' => 'Academia Força Total', 'peso' => 22],
        ],
        'Oficina Mecânica' => [
            ['nome' => 'Oficina Mecânica São Jorge', 'peso' => 28],
            ['nome' => 'Auto Center Bagé', 'peso' => 26],
            ['nome' => 'Mecânica do João', 'peso' => 22],
        ],
        'Posto de Combustível' => [
            ['nome' => 'Posto Ipiranga', 'peso' => 30],
            ['nome' => 'Posto Shell', 'peso' => 26],
            ['nome' => 'Posto BR', 'peso' => 22],
        ],
        'Ótica' => [
            ['nome' => 'Ótica Visão', 'peso' => 32, 'variacoes' => ['Otica Visao', 'ótica visão']],
            ['nome' => 'Ótica Bagé', 'peso' => 24],
            ['nome' => 'Ótica Popular', 'peso' => 20],
        ],
        'Colunista' => [
            ['nome' => 'Carlos Mendes', 'peso' => 35],
            ['nome' => 'Ana Paula Ribeiro', 'peso' => 28],
            ['nome' => 'Roberto Silva', 'peso' => 20],
        ],
        'Jornalista' => [
            ['nome' => 'Mariana Costa', 'peso' => 32],
            ['nome' => 'Fernando Alves', 'peso' => 26],
            ['nome' => 'Patrícia Lima', 'peso' => 22],
        ],
        'Nutricionista' => [
            ['nome' => 'Dra. Helena Nutri', 'peso' => 30],
            ['nome' => 'Dr. Paulo Nutricionista', 'peso' => 26],
            ['nome' => 'Clínica Vida Saudável', 'peso' => 22],
        ],
        'Psicólogo(a)' => [
            ['nome' => 'Dra. Carla Psicologia', 'peso' => 30],
            ['nome' => 'Dr. Marcos Terapia', 'peso' => 26],
            ['nome' => 'Espaço Mente Saudável', 'peso' => 22],
        ],
        'Hotel' => [
            ['nome' => 'Hotel Fronteira', 'peso' => 32],
            ['nome' => 'Hotel Bagé Plaza', 'peso' => 26],
            ['nome' => 'Pousada do Centro', 'peso' => 20],
        ],
        'PetShop' => [
            ['nome' => 'Pet Shop Amigo Fiel', 'peso' => 34, 'variacoes' => ['Petshop Amigo Fiel', 'PET SHOP AMIGO FIEL']],
            ['nome' => 'Mundo Animal Bagé', 'peso' => 24],
            ['nome' => 'Pet Center', 'peso' => 20],
        ],
        'Salão de beleza feminino' => [
            ['nome' => 'Salão Beleza Pura', 'peso' => 30],
            ['nome' => 'Studio Hair Bagé', 'peso' => 26],
            ['nome' => 'Espaço Feminino', 'peso' => 22],
        ],
        'Provedor de Internet' => [
            ['nome' => 'Net Bagé', 'peso' => 32],
            ['nome' => 'Fibra Fronteira', 'peso' => 28],
            ['nome' => 'Conecta RS', 'peso' => 20],
        ],
        'Clínica Odontológica' => [
            ['nome' => 'Odonto Bagé', 'peso' => 30],
            ['nome' => 'Sorriso Perfeito', 'peso' => 26],
            ['nome' => 'Clínica Dental Care', 'peso' => 22],
        ],
        'Loja de Calçados' => [
            ['nome' => 'Calçados Bagé', 'peso' => 30],
            ['nome' => 'Sapataria do Centro', 'peso' => 26],
            ['nome' => 'Passo Certo', 'peso' => 22],
        ],
        'Moda Feminina' => [
            ['nome' => 'Boutique Elegance', 'peso' => 30],
            ['nome' => 'Moda & Estilo Bagé', 'peso' => 26],
            ['nome' => 'Loja Dona Maria', 'peso' => 22],
        ],
        'Revenda de Veículos Novos' => [
            ['nome' => 'Concessionária Bagé Motors', 'peso' => 32],
            ['nome' => 'Auto Premium', 'peso' => 26],
            ['nome' => 'Fronteira Veículos', 'peso' => 22],
        ],
        'Universidade/Centro Universitário' => [
            ['nome' => 'UNIPAC Bagé', 'peso' => 35],
            ['nome' => 'Centro Universitário da Fronteira', 'peso' => 28],
            ['nome' => 'Faculdade Integrada', 'peso' => 20],
        ],
        'Rádio' => [
            ['nome' => 'Rádio Fronteira FM', 'peso' => 32],
            ['nome' => 'Rádio Bagé', 'peso' => 28],
            ['nome' => 'FM 95 Bagé', 'peso' => 22],
        ],
    ],
];
