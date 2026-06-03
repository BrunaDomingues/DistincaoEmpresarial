<style>
    /* Alinhamento com menu/header (max-w-85) e selects de filtro legíveis */
    .relatorio-filtro-select {
        width: 100%;
        min-width: 12rem;
        padding: 0.5rem 0.75rem;
    }
    .relatorio-filtro-select--wide {
        min-width: 16rem;
    }
    @media (min-width: 640px) {
        .relatorio-filtro-select {
            width: auto;
        }
    }

    @media (max-width: 1040px) {
        .relatorio-acoes {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 1rem !important;
        }
        .relatorio-acoes form {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.5rem !important;
            width: 100% !important;
        }
        .relatorio-acoes form select {
            width: 100% !important;
            min-width: 0 !important;
            padding: 0.5rem 0.75rem !important;
        }
        .relatorio-acoes .btn-relatorio {
            display: block !important;
            width: 100% !important;
            text-align: center !important;
            box-sizing: border-box !important;
        }
        .relatorio-form-mobile label {
            display: block;
            margin-bottom: 0.25rem;
        }
        .relatorio-form-mobile select {
            width: 100% !important;
            min-width: 0 !important;
            padding: 0.5rem 0.75rem !important;
        }
        .relatorio-form-mobile .btn-relatorio {
            display: block !important;
            width: 100% !important;
            margin-top: 0.75rem !important;
            text-align: center !important;
        }
    }
</style>
