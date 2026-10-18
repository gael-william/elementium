@extends('layouts.site')

@php
    $elements = [
        'feu' => ['name' => 'Feu', 'verb' => 'Créer', 'icon' => 'bi-fire', 'image' => 'images/portfolio/birdie.webp',
            'text' => 'L’étincelle de chaque projet. Nous donnons forme aux idées : personnages, univers, illustrations et animations 2D/3D qui brûlent l’écran.',
            'items' => ['Animation 2D/3D', 'Character design', 'Concept art', 'Illustration']],
        'eau' => ['name' => 'Eau', 'verb' => 'Faire vibrer', 'icon' => 'bi-water', 'image' => 'images/portfolio/sound-design.webp',
            'text' => 'La fluidité du son et du mouvement. Mixage, sound design, musique originale et motion design qui portent l’émotion.',
            'items' => ['Production audio', 'Sound design', 'Voix off', 'Motion design']],
        'terre' => ['name' => 'Terre', 'verb' => 'Construire', 'icon' => 'bi-tree', 'image' => 'images/portfolio/jeu-vr.webp',
            'text' => 'L’ancrage technique. Jeux vidéo, expériences VR/AR et post-production : des œuvres solides, prêtes à être vécues.',
            'items' => ['Jeux vidéo Unity / Unreal', 'Réalité virtuelle', 'Post-production', 'Étalonnage & VFX']],
    ];
    $socialIcons = ['whatsapp' => 'bi-whatsapp', 'tiktok' => 'bi-tiktok', 'facebook' => 'bi-facebook', 'instagram' => 'bi-instagram', 'youtube' => 'bi-youtube', 'linkedin' => 'bi-linkedin', 'x' => 'bi-twitter-x'];
@endphp

@section('content')
    {{-- ================================================= Préchargement --}}
    <div class="preloader" aria-hidden="true">
        <div class="preloader__inner">
            <img src="{{ asset('images/logo.webp') }}" alt="" class="preloader__logo">
            <div class="preloader__bars"><span class="feu"></span><span class="eau"></span><span class="terre"></span></div>
            <div class="preloader__count"><span>0</span>%</div>
        </div>
    </div>

    <div class="cursor" aria-hidden="true"><div class="cursor__dot"></div><div class="cursor__ring"><span class="cursor__label"></span></div></div>
    <div class="scroll-progress" aria-hidden="true"></div>
    <div class="grain" aria-hidden="true"></div>

    {{-- ================================================= Navigation --}}
    <header class="nav">
        <a href="#hero" class="nav__brand" data-cursor="Accueil">
            <img src="{{ asset('images/logo.webp') }}" alt="">
            <span>Élémentium<em>Studio</em></span>
        </a>
        <nav class="nav__links" aria-label="Navigation principale">
            <a href="#about">À propos</a>
            <a href="#elements">Univers</a>
            <a href="#portfolio">Réalisations</a>
            <a href="#videos">Vidéos</a>
            <a href="#team">Équipe</a>
        </nav>
        <div class="nav__actions">
            <button class="icon-btn theme-toggle" type="button" aria-label="Changer de thème"><i class="bi bi-moon-stars"></i></button>
            <a href="#contact" class="btn btn--small btn--primary magnetic">Démarrer un projet</a>
            <button class="burger" type="button" aria-label="Ouvrir le menu" aria-expanded="false"><span></span><span></span></button>
        </div>
    </header>

    <div class="menu" aria-hidden="true">
        <nav class="menu__links">
            @foreach (['hero' => 'Accueil', 'about' => 'À propos', 'elements' => 'Univers', 'portfolio' => 'Réalisations', 'videos' => 'Vidéos', 'team' => 'Équipe', 'contact' => 'Contact'] as $id => $label)
                <a href="#{{ $id }}"><small>0{{ $loop->iteration }}</small>{{ $label }}</a>
            @endforeach
        </nav>
        <div class="menu__footer">
            <a href="mailto:{{ config('studio.email') }}">{{ config('studio.email') }}</a>
            <div class="socials">
                @foreach (config('studio.socials') as $network => $url)
                    <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}"><i class="bi {{ $socialIcons[$network] }}"></i></a>
                @endforeach
            </div>
        </div>
    </div>

    <main>
        {{-- ================================================= Hero 3D --}}
        <section id="hero" class="hero">
            <div class="hero__bg" style="background-image:url('{{ asset('images/hero.webp') }}')"></div>
            <canvas class="hero__canvas" aria-hidden="true"></canvas>
            <div class="hero__content">
                <p class="eyebrow hero__eyebrow"><span class="dot"></span> Studio de création audiovisuelle · Ouagadougou</p>
                <h1 class="hero__title">
                    <span class="line"><span class="split">Élémentium</span></span>
                    <span class="line line--outline"><span class="split">Studio</span></span>
                </h1>
                <p class="hero__lead">Nous créons <span class="typed" data-words="des animations 2D/3D|des illustrations uniques|de la production audio|des jeux vidéo|des univers immersifs">des animations 2D/3D</span><span class="typed-caret">|</span></p>
                <div class="hero__cta">
                    <a href="#portfolio" class="btn btn--primary magnetic" data-cursor="Explorer">Voir nos réalisations <i class="bi bi-arrow-down-right"></i></a>
                    <a href="#videos" class="btn btn--ghost magnetic"><i class="bi bi-play-fill"></i> Showreel</a>
                </div>
            </div>
            <div class="hero__side">
                @foreach (config('studio.socials') as $network => $url)
                    <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}"><i class="bi {{ $socialIcons[$network] }}"></i></a>
                @endforeach
            </div>
            <div class="hero__elements">
                <span class="feu"><i class="bi bi-fire"></i> Feu</span>
                <span class="eau"><i class="bi bi-water"></i> Eau</span>
                <span class="terre"><i class="bi bi-tree"></i> Terre</span>
            </div>
            <a href="#about" class="hero__scroll" aria-label="Défiler"><span></span></a>
        </section>

        {{-- ================================================= Bandeau défilant --}}
        <div class="marquee" aria-hidden="true">
            <div class="marquee__row">
                <div class="marquee__track">
                    @for ($i = 0; $i < 2; $i++)
                        @foreach (['Animation 2D/3D', 'Production audio', 'Jeux vidéo', 'Illustration', 'Motion design', 'Post-production', 'Réalité virtuelle'] as $word)
                            <span>{{ $word }}</span><i class="bi bi-asterisk"></i>
                        @endforeach
                    @endfor
                </div>
            </div>
        </div>

        {{-- ================================================= À propos --}}
        <section id="about" class="section about">
            <div class="container about__grid">
                <div class="about__visual">
                    <div class="tilt about__frame" data-tilt="8">
                        <img src="{{ asset('images/about.webp') }}" alt="L’équipe d’Élémentium Studio" class="about__img">
                        <div class="about__badge">
                            <img src="{{ asset('images/logo.webp') }}" alt="">
                            <svg viewBox="0 0 200 200"><defs><path id="circlePath" d="M100,100 m-80,0 a80,80 0 1,1 160,0 a80,80 0 1,1 -160,0"/></defs>
                                <text><textPath href="#circlePath">FEU · EAU · TERRE · ÉLÉMENTIUM STUDIO · </textPath></text></svg>
                        </div>
                    </div>
                </div>
                <div class="about__content">
                    <p class="eyebrow reveal">À propos</p>
                    <h2 class="section-title reveal-lines">Nous transformons vos idées en <span class="grad">expériences visuelles</span> captivantes.</h2>
                    <p class="lead reveal">Élémentium Studio est une agence créative spécialisée dans la création de contenu audiovisuel innovant. Depuis notre création, nous repoussons les limites de la créativité numérique en combinant <strong>technologie de pointe</strong> et <strong>vision artistique</strong>.</p>
                    <p class="reveal">Notre équipe pluridisciplinaire unit expertise technique et créativité artistique : de l’animation 2D/3D aux jeux vidéo, en passant par la production audio et l’illustration, nous donnons vie à vos projets les plus ambitieux.</p>
                    <div class="stats">
                        <div class="stat reveal"><strong data-count="{{ config('studio.founded') }}" data-plain>{{ config('studio.founded') }}</strong><span>Année de fondation</span></div>
                        <div class="stat reveal"><strong><span data-count="{{ config('studio.team_size') }}">{{ config('studio.team_size') }}</span>+</strong><span>Experts créatifs</span></div>
                        <div class="stat reveal"><strong><span data-count="{{ $portfolio->count() }}">{{ $portfolio->count() }}</span>+</strong><span>Réalisations</span></div>
                        <div class="stat reveal"><strong>24/7</strong><span>À votre écoute</span></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================================================= Univers : défilement horizontal 3D --}}
        <section id="elements" class="elements">
            <div class="elements__intro container">
                <p class="eyebrow reveal">Notre univers</p>
                <h2 class="section-title reveal-lines">Trois éléments,<br>une seule <span class="grad">vision</span>.</h2>
            </div>
            <div class="elements__pin">
                <div class="elements__track">
                    @foreach ($elements as $key => $el)
                        <article class="element-panel element-panel--{{ $key }}">
                            <div class="element-panel__inner">
                                <div class="element-panel__text">
                                    <span class="element-panel__num">0{{ $loop->iteration }} / 03</span>
                                    <h3><i class="bi {{ $el['icon'] }}"></i> {{ $el['name'] }}</h3>
                                    <p class="element-panel__verb">{{ $el['verb'] }}</p>
                                    <p>{{ $el['text'] }}</p>
                                    <ul>
                                        @foreach ($el['items'] as $item)<li>{{ $item }}</li>@endforeach
                                    </ul>
                                </div>
                                <div class="element-panel__media">
                                    <img src="{{ asset($el['image']) }}" alt="" loading="lazy">
                                </div>
                            </div>
                            <span class="element-panel__giant" aria-hidden="true">{{ $el['name'] }}</span>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ================================================= Expertise --}}
        <section id="services" class="section services">
            <div class="container">
                <div class="section-head">
                    <p class="eyebrow reveal">Expertise technique</p>
                    <h2 class="section-title reveal-lines">Des compétences maîtrisées pour des productions <span class="grad">d’exception</span>.</h2>
                </div>
                <div class="services__grid">
                    @foreach (config('studio.services') as $service)
                        <article class="service-card tilt el-{{ $service['element'] }}" data-tilt="10">
                            <span class="service-card__glare"></span>
                            <span class="service-card__num">0{{ $loop->iteration }}</span>
                            <i class="bi {{ $service['icon'] }} service-card__icon"></i>
                            <h3>{{ $service['title'] }}</h3>
                            <p>{{ $service['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ================================================= Réalisations --}}
        <section id="portfolio" class="section portfolio">
            <div class="container">
                <div class="section-head section-head--row">
                    <div>
                        <p class="eyebrow reveal">Nos réalisations</p>
                        <h2 class="section-title reveal-lines">Portfolio <span class="grad">créatif</span></h2>
                    </div>
                    <div class="filters" data-filters="portfolio" role="tablist">
                        <button class="filter is-active" data-filter="*" type="button">Tous</button>
                        @foreach ($portfolioCategories as $key => $label)
                            <button class="filter" data-filter="{{ $key }}" type="button">{{ $label }}</button>
                        @endforeach
                    </div>
                </div>
                <div class="portfolio__grid">
                    @forelse ($portfolio as $item)
                        <figure class="work" data-category="{{ $item->category }}" data-cursor="Voir"
                                data-src="{{ $item->image_url }}" data-title="{{ $item->title }}" data-text="{{ collect([$item->subtitle, $item->description])->filter()->join(" — ") }}" data-link="{{ $item->link }}">
                            <button type="button" class="work__open" aria-label="Agrandir {{ $item->title }}">
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy">
                            </button>
                            <figcaption>
                                <span class="tag">{{ $item->category_label }}</span>
                                <h3>{{ $item->title }}</h3>
                                <p>{{ $item->subtitle }}</p>
                            </figcaption>
                        </figure>
                    @empty
                        <p class="empty">Les réalisations arrivent bientôt.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ================================================= Vidéos --}}
        <section id="videos" class="section videos">
            <div class="container">
                <div class="section-head section-head--row">
                    <div>
                        <p class="eyebrow reveal">Élémentium vidéo</p>
                        <h2 class="section-title reveal-lines">La <span class="grad">salle de projection</span></h2>
                        <p class="reveal muted">Découvrez nos créations audiovisuelles. Vous avez une création à partager ? Proposez-la à la communauté.</p>
                    </div>
                    <button class="btn btn--ghost magnetic" type="button" data-open="submit-modal"><i class="bi bi-cloud-arrow-up"></i> Proposer une vidéo</button>
                </div>

                <div class="filters" data-filters="videos" role="tablist">
                    <button class="filter is-active" data-filter="*" type="button">Toutes</button>
                    @foreach ($videoCategories as $key => $label)
                        @if ($videos->contains('category', $key))
                            <button class="filter" data-filter="{{ $key }}" type="button">{{ $label }}</button>
                        @endif
                    @endforeach
                </div>

                <div class="videos__grid">
                    @forelse ($videos as $video)
                        <article class="video-card {{ $loop->first ? 'video-card--featured' : '' }}" data-category="{{ $video->category }}" data-video="{{ $video->id }}" data-cursor="Lire">
                            <button type="button" class="video-card__thumb" aria-label="Lire {{ $video->title }}">
                                @if ($video->thumbnail_url)
                                    <img src="{{ $video->thumbnail_url }}" alt="" loading="lazy">
                                @else
                                    <span class="video-card__placeholder"><i class="bi bi-film"></i></span>
                                @endif
                                <span class="video-card__play"><i class="bi bi-play-fill"></i></span>
                                @if ($video->formatted_duration)<span class="video-card__duration">{{ $video->formatted_duration }}</span>@endif
                            </button>
                            <div class="video-card__info">
                                <span class="tag">{{ $video->category_label }}</span>
                                <h3>{{ $video->title }}</h3>
                                <div class="video-card__meta">
                                    <span><i class="bi bi-eye"></i> <span data-views>{{ $video->views }}</span></span>
                                    <button type="button" class="like-btn" data-like="{{ $video->id }}" aria-label="J’aime"><i class="bi bi-heart"></i> <span data-likes>{{ $video->likes }}</span></button>
                                    <button type="button" class="share-btn" data-share="{{ $video->id }}" data-title="{{ $video->title }}" aria-label="Partager"><i class="bi bi-share"></i></button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="empty">Aucune vidéo pour le moment — revenez bientôt !</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ================================================= Équipe --}}
        <section id="team" class="section team">
            <div class="container">
                <div class="section-head">
                    <p class="eyebrow reveal">Notre équipe créative</p>
                    <h2 class="section-title reveal-lines">Les <span class="grad">alchimistes</span> derrière chaque projet.</h2>
                </div>
                <div class="team__grid">
                    @foreach ($team as $member)
                        <article class="member tilt el-{{ $member->element }}" data-tilt="6">
                            <div class="member__photo">
                                @if ($member->photo_url)
                                    <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" loading="lazy">
                                @else
                                    <span class="member__initials">{{ mb_substr($member->name, 0, 1) }}</span>
                                @endif
                                @if (! empty($member->socials))
                                    <div class="member__socials">
                                        @foreach ($member->socials as $network => $url)
                                            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}"><i class="bi {{ $socialIcons[$network] ?? 'bi-link-45deg' }}"></i></a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="member__info">
                                <h3>{{ $member->name }}</h3>
                                <span class="member__role">{{ $member->role }}</span>
                                <p>{{ $member->bio }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ================================================= Appel à l'action --}}
        <section class="cta">
            <div class="cta__glow" aria-hidden="true"></div>
            <div class="container">
                <p class="eyebrow reveal">Un projet en tête ?</p>
                <h2 class="cta__title"><span class="split-words">Donnons vie à votre</span> <span class="grad">prochaine idée.</span></h2>
                <a href="#contact" class="cta__button magnetic" data-cursor="Go">
                    <span>Parlons-en</span><i class="bi bi-arrow-up-right"></i>
                </a>
            </div>
        </section>

        {{-- ================================================= Contact --}}
        <section id="contact" class="section contact">
            <div class="container contact__grid">
                <div class="contact__info">
                    <p class="eyebrow reveal">Contactez-nous</p>
                    <h2 class="section-title reveal-lines">Discutons de vos <span class="grad">idées</span>.</h2>
                    <p class="muted reveal">Prêt à donner vie à votre projet créatif ? Racontez-nous tout, nous vous répondons sous 24 h.</p>
                    <ul class="contact__list">
                        <li class="reveal"><i class="bi bi-geo-alt"></i><div><strong>Notre studio</strong><span>{{ config('studio.address') }}</span></div></li>
                        <li class="reveal"><i class="bi bi-telephone"></i><div><strong>Appelez-nous</strong>
                            @foreach (config('studio.phones') as $phone)<a href="tel:{{ str_replace(' ', '', $phone) }}">{{ $phone }}</a>@endforeach
                        </div></li>
                        <li class="reveal"><i class="bi bi-envelope"></i><div><strong>Écrivez-nous</strong><a href="mailto:{{ config('studio.email') }}">{{ config('studio.email') }}</a></div></li>
                        <li class="reveal"><i class="bi bi-clock"></i><div><strong>Horaires</strong><span>{{ config('studio.hours') }}</span></div></li>
                    </ul>
                    <div class="contact__map reveal">
                        <iframe src="{{ config('studio.map_embed') }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Carte : Wemtenga, Ouagadougou"></iframe>
                    </div>
                </div>

                <form class="contact__form glass ajax-form" action="{{ route('contact.store') }}" method="post" novalidate>
                    @csrf
                    <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <div class="field-row">
                        <label class="field"><input type="text" name="name" required placeholder=" " autocomplete="name"><span>Votre nom *</span></label>
                        <label class="field"><input type="email" name="email" required placeholder=" " autocomplete="email"><span>Votre e-mail *</span></label>
                    </div>
                    <div class="field-row">
                        <label class="field"><input type="text" name="company" placeholder=" " autocomplete="organization"><span>Entreprise</span></label>
                        <label class="field field--select">
                            <select name="service" required>
                                <option value="" selected disabled></option>
                                @foreach ($services as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                            </select>
                            <span>Service souhaité *</span>
                        </label>
                    </div>
                    <label class="field"><input type="text" name="subject" required placeholder=" "><span>Sujet *</span></label>
                    <label class="field"><textarea name="message" rows="6" required placeholder=" "></textarea><span>Décrivez votre projet : vision, objectifs, budget, délais… *</span></label>
                    <button type="submit" class="btn btn--primary btn--block magnetic"><span>Envoyer le message</span> <i class="bi bi-send"></i></button>
                    <p class="form-status" role="status"></p>
                </form>
            </div>
        </section>
    </main>

    {{-- ================================================= Pied de page --}}
    <footer class="footer">
        <div class="container">
            <div class="footer__top">
                <div class="footer__brand">
                    <img src="{{ asset('images/logo.webp') }}" alt="" width="56" height="56">
                    <p>Votre partenaire créatif pour des expériences audiovisuelles exceptionnelles. Nous transformons vos idées en réalité immersive.</p>
                    <div class="socials">
                        @foreach (config('studio.socials') as $network => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}"><i class="bi {{ $socialIcons[$network] }}"></i></a>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h4>Services</h4>
                    <ul>@foreach (config('studio.services') as $service)<li><a href="#services">{{ $service['title'] }}</a></li>@endforeach</ul>
                </div>
                <div>
                    <h4>Studio</h4>
                    <ul>
                        <li><a href="#about">À propos</a></li>
                        <li><a href="#team">Notre équipe</a></li>
                        <li><a href="#portfolio">Portfolio</a></li>
                        <li><a href="#videos">Vidéos</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Newsletter</h4>
                    <p>Recevez nos dernières créations et innovations.</p>
                    <form class="newsletter ajax-form" action="{{ route('newsletter.store') }}" method="post">
                        @csrf
                        <input type="email" name="email" required placeholder="Votre e-mail" aria-label="Votre e-mail">
                        <button type="submit" aria-label="S’abonner"><i class="bi bi-arrow-right"></i></button>
                        <p class="form-status" role="status"></p>
                    </form>
                </div>
            </div>
            <div class="footer__giant" aria-hidden="true">Élémentium</div>
            <div class="footer__bottom">
                <span>© {{ date('Y') }} Élémentium Studio — Tous droits réservés.</span>
                <span>Fait avec <i class="bi bi-fire"></i><i class="bi bi-droplet"></i><i class="bi bi-tree"></i> à Ouagadougou</span>
            </div>
        </div>
    </footer>

    <a href="#hero" class="to-top" aria-label="Revenir en haut"><i class="bi bi-arrow-up"></i></a>
    <div class="toast" role="status" aria-live="polite"></div>

    {{-- ================================================= Fenêtres modales --}}
    <div class="modal lightbox" id="lightbox" aria-hidden="true" role="dialog" aria-label="Aperçu">
        <button class="modal__close" type="button" data-close aria-label="Fermer"><i class="bi bi-x-lg"></i></button>
        <button class="lightbox__nav lightbox__nav--prev" type="button" aria-label="Précédent"><i class="bi bi-arrow-left"></i></button>
        <figure class="lightbox__figure">
            <img src="" alt="">
            <figcaption><h3></h3><p></p><a href="#" target="_blank" rel="noopener" class="btn btn--small btn--ghost" hidden>Voir le projet <i class="bi bi-arrow-up-right"></i></a></figcaption>
        </figure>
        <button class="lightbox__nav lightbox__nav--next" type="button" aria-label="Suivant"><i class="bi bi-arrow-right"></i></button>
    </div>

    <div class="modal player" id="player" aria-hidden="true" role="dialog" aria-label="Lecteur vidéo">
        <div class="modal__backdrop" data-close></div>
        <button class="modal__close" type="button" data-close aria-label="Fermer"><i class="bi bi-x-lg"></i></button>
        <div class="player__box">
            <div class="player__screen"></div>
            <div class="player__info">
                <div>
                    <span class="tag player__category"></span>
                    <h3 class="player__title"></h3>
                    <p class="player__desc muted"></p>
                </div>
                <div class="player__actions">
                    <span class="muted"><i class="bi bi-eye"></i> <span class="player__views">0</span></span>
                    <button type="button" class="like-btn player__like"><i class="bi bi-heart"></i> <span class="player__likes">0</span></button>
                    <button type="button" class="share-btn player__share" aria-label="Partager"><i class="bi bi-share"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal submit" id="submit-modal" aria-hidden="true" role="dialog" aria-labelledby="submit-title">
        <div class="modal__backdrop" data-close></div>
        <div class="submit__box glass">
            <button class="modal__close" type="button" data-close aria-label="Fermer"><i class="bi bi-x-lg"></i></button>
            <h3 id="submit-title">Proposer une vidéo</h3>
            <p class="muted">Partagez votre création : elle sera publiée après validation par notre équipe.</p>
            <div class="tabs" role="tablist">
                <button type="button" class="tab is-active" data-tab="link"><i class="bi bi-link-45deg"></i> Lien vidéo</button>
                <button type="button" class="tab" data-tab="file"><i class="bi bi-upload"></i> Fichier</button>
            </div>
            <form class="ajax-form submit__form" action="{{ route('videos.store') }}" method="post" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                <div class="tab-panel is-active" data-panel="link">
                    <label class="field"><input type="url" name="url" placeholder=" "><span>Lien YouTube, Vimeo ou direct *</span></label>
                </div>
                <div class="tab-panel" data-panel="file">
                    <label class="dropzone">
                        <input type="file" name="file" accept="video/mp4,video/webm,video/quicktime">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <strong>Glissez votre vidéo ici</strong>
                        <span class="dropzone__name">ou cliquez pour parcourir (MP4, WebM, MOV — 200 Mo max)</span>
                    </label>
                </div>
                <label class="field"><input type="text" name="title" required placeholder=" "><span>Titre de la vidéo *</span></label>
                <label class="field field--select">
                    <select name="category" required>
                        <option value="" selected disabled></option>
                        @foreach ($videoCategories as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                    </select>
                    <span>Catégorie *</span>
                </label>
                <label class="field"><textarea name="description" rows="3" placeholder=" "></textarea><span>Description</span></label>
                <div class="field-row">
                    <label class="field"><input type="text" name="submitter_name" required placeholder=" "><span>Votre nom *</span></label>
                    <label class="field"><input type="email" name="submitter_email" required placeholder=" "><span>Votre e-mail *</span></label>
                </div>
                <div class="progress" hidden><span></span></div>
                <button type="submit" class="btn btn--primary btn--block"><span>Envoyer ma vidéo</span> <i class="bi bi-send"></i></button>
                <p class="form-status" role="status"></p>
            </form>
        </div>
    </div>
@endsection
