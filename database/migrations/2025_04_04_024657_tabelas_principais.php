<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('formularios', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('formulario_passos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formulario_id')->constrained('formularios')->onDelete('cascade');
            $table->string('titulo');
            $table->integer('ordem');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('formulario_perguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('passo_id')->constrained('formulario_passos')->onDelete('cascade');
            $table->string('tipo'); // texto, radio, checkbox, select...
            $table->string('pergunta');
            $table->boolean('obrigatorio')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('formulario_opcoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pergunta_id')->constrained('formulario_perguntas')->onDelete('cascade');
            $table->string('opcao');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('formulario_respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pergunta_id')->constrained('formulario_perguntas')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->text('resposta');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('formulario_respostas_tratadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resposta_id')->constrained('formulario_respostas')->onDelete('cascade');
            $table->text('resposta_tratada');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('formulario_respostas_tratadas');
        Schema::dropIfExists('formulario_respostas');
        Schema::dropIfExists('formulario_opcoes');
        Schema::dropIfExists('formulario_perguntas');
        Schema::dropIfExists('formulario_passos');
        Schema::dropIfExists('formularios');
    }
};
