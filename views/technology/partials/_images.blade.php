<details class="form-accordion">

    <summary class="form-accordion__trigger">
        {{ __('technology.part2_title') }}
    </summary>

    <div class="form-accordion__body">

        <div class="form-grid form-grid--2">

            {{-- =========================
                 IMAGEM
            ========================== --}}
            <div class="form-field">

                <label class="form-label">
                    {{ __('technology.image') }}
                </label>

                {{-- Imagem atual --}}
                @if (!empty($tecnologia->imagem_lateral))

                    <div style="margin-bottom: 15px;">

                        <p class="form-hint">
                            Imagem atual:
                        </p>

                        <img
                            src="{{ $tecnologia->imagem_url }}"
                            alt="Imagem atual da tecnologia"
                            style="
                                width: 180px;
                                height: 120px;
                                object-fit: contain;
                                border: 1px solid #ddd;
                                border-radius: 6px;
                                padding: 4px;
                                display: block;
                                background: #f8f9fa;
                            "
                        >

                    </div>

                @else

                    <p class="form-hint">
                        Nenhuma imagem cadastrada.
                    </p>

                @endif

                {{-- Nova imagem --}}
                <input
                    type="file"
                    name="imagem_lateral"
                    class="form-input"
                    accept="image/*"
                >

                <p class="form-hint">
                    Selecione uma nova imagem somente se quiser substituir a atual.
                </p>

            </div>


            {{-- =========================
                 VÍDEO
            ========================== --}}
            <div class="form-field">

                <label class="form-label">
                    {{ __('technology.video_url') }}
                </label>

                {{-- Vídeo atual --}}
                @if (!empty($tecnologia->url_youtube))

                    <div style="margin-bottom: 15px;">

                        <p class="form-hint">
                            Vídeo atual:
                        </p>

                        <a
                            href="{{ $tecnologia->url_youtube }}"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            ▶ Abrir vídeo atual
                        </a>

                    </div>

                @endif


                {{-- URL para alterar --}}
                <input
                    type="url"
                    name="url_video"
                    class="form-input"
                    placeholder="https://youtu.be/VIDEO-ID"
                    value="{{ old('url_video', $tecnologia->url_youtube ?? '') }}"
                >

                <p class="form-hint">
                    Altere a URL acima para substituir o vídeo.
                </p>


                {{-- Remover vídeo --}}
                @if (!empty($tecnologia->url_youtube))

                    <label
                        style="
                            display: flex;
                            align-items: center;
                            gap: 8px;
                            margin-top: 12px;
                            cursor: pointer;
                        "
                    >

                        <input
                            type="checkbox"
                            name="remover_video"
                            value="1"
                            {{ old('remover_video') ? 'checked' : '' }}
                        >

                        Remover vídeo atual

                    </label>

                @endif

            </div>

        </div>


        {{-- =========================
             DESCRIÇÃO
        ========================== --}}
        <div
            class="form-field"
            style="margin-top: 20px;"
        >

            <label
                for="descricao_imagem_video"
                class="form-label"
            >
                Descrição da imagem ou vídeo
            </label>

            <textarea
                id="descricao_imagem_video"
                name="descricao_imagem_video"
                class="form-input"
                rows="5"
                placeholder="Descreva a imagem ou o conteúdo do vídeo..."
            >{{ old('descricao_imagem_video', $tecnologia->descricao_imagem_video ?? '') }}</textarea>

            <p class="form-hint">
                Informe uma descrição da imagem ou do vídeo.
            </p>

        </div>

    </div>

</details>