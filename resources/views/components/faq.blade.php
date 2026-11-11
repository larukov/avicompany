@php
    $faqItems = [
        [
            'question' => 'Какие документы нужны для перелета?',
            'answer' =>
                'Для внутренних рейсов достаточно паспорта РФ. Для международных — загранпаспорт и виза (при необходимости).',
        ],
        [
            'question' => 'Правила перевозки детей',
            'answer' =>
                'Дети до 2 лет летят бесплатно без отдельного места. От 2 до 12 лет — скидка 50%. С 12 лет — полный тариф.',
        ],
        [
            'question' => 'Правила сдачи билетов',
            'answer' =>
                'Возврат возможен за 24 часа до вылета без комиссии. Позже — удерживается 25% от стоимости билета.',
        ],
        [
            'question' => 'Какие нормы провоза багажа и ручной клади?',
            'answer' => 'Ручная кладь: до 10 кг. Багаж: до 23 кг в эконом-классе, до 32 кг в бизнес-классе.',
        ],
    ];
@endphp
<section class="container mt-5 mb-5 text-white fw-bold">
    <div class="row" id="help">
        <div class="bg-primary col-lg-10 offset-lg-1 rounded-3 p-4">
            <h3 class="text-center mb-4">Ответы на вопросы</h3>
            <div class="accordion accordion-flush" id="accordionFlushExample">
                @foreach ($faqItems as $index => $item)
                    <div class="accordion-item mt-4">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed text-white" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq-{{ $index }}">
                                {{ $item['question'] }}
                            </button>
                        </h2>
                        <div id="faq-{{ $index }}" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                {{ $item['answer'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>