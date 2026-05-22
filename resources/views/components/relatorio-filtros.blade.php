<style>
    /* Desktop (padrão) */
    .relatorio-filtros-wrap {
        margin-bottom: 1rem;
    }
    .relatorio-toolbar {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .relatorio-toolbar-form {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }
    .relatorio-toolbar-form label {
        font-weight: 500;
        color: #374151;
        white-space: nowrap;
    }
    .dark .relatorio-toolbar-form label {
        color: #d1d5db;
    }
    .relatorio-toolbar-form select {
        min-width: 160px;
        padding: 0.25rem 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.25rem;
        background: #fff;
    }
    .dark .relatorio-toolbar-form select {
        border-color: #374151;
        background: #1f2937;
        color: #fff;
    }
    .relatorio-toolbar-btn {
        display: inline-block;
        padding: 0.5rem 1rem;
        background-color: #2563eb;
        color: #fff !important;
        text-decoration: none !important;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        line-height: 1.25rem;
        white-space: nowrap;
        transition: background-color 0.15s;
    }
    .relatorio-toolbar-btn:hover {
        background-color: #1d4ed8;
        color: #fff !important;
    }
    .relatorio-form-stack {
        margin-bottom: 0;
    }
    .relatorio-form-stack label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.25rem;
        color: #374151;
    }
    .dark .relatorio-form-stack label {
        color: #d1d5db;
    }
    .relatorio-form-stack select {
        display: block;
        width: 100%;
        max-width: 28rem;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.25rem;
    }
    .dark .relatorio-form-stack select {
        border-color: #374151;
        background: #1f2937;
        color: #fff;
    }
    .relatorio-form-stack .relatorio-toolbar-btn {
        margin-top: 0.75rem;
    }

    /* Mobile */
    @media (max-width: 1040px) {
        .relatorio-page {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .relatorio-filtros-wrap {
            margin-bottom: 0;
            border-radius: 0.5rem;
            background-color: #fff;
            padding: 1rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        }
        .dark .relatorio-filtros-wrap {
            background-color: #1f2937;
        }
        .relatorio-toolbar {
            flex-direction: column;
            align-items: stretch;
            gap: 1.25rem;
        }
        .relatorio-toolbar-form {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
            gap: 0.75rem;
        }
        .relatorio-toolbar-form label {
            white-space: normal;
        }
        .relatorio-toolbar-form select {
            width: 100%;
            min-width: 0;
            padding: 0.625rem 0.75rem;
        }
        .relatorio-toolbar-btn {
            display: block;
            width: 100%;
            text-align: center;
            padding: 0.625rem 1rem;
            box-sizing: border-box;
        }
        .relatorio-form-stack select {
            max-width: none;
        }
        .relatorio-form-stack .relatorio-toolbar-btn {
            margin-top: 1rem;
            width: 100%;
        }
    }
</style>

<div class="relatorio-filtros-wrap">
    {{ $slot }}
</div>
