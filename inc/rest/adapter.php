<?php
/**
 * AI content generation adapters.
 *
 * Provides the adapter interface and default implementations for AI providers.
 *
 * @package PF2\Rest
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'pf2_str_limit' ) ) {
        /**
         * Limit a string to a maximum length without breaking words.
         *
         * @param string $text     Input text.
         * @param int    $limit    Maximum length.
         * @param string $ellipsis Ellipsis to append.
         *
         * @return string
         */
        function pf2_str_limit( $text, $limit, $ellipsis = '…' ) {
                $text = wp_strip_all_tags( (string) $text );

                if ( mb_strlen( $text ) <= $limit ) {
                        return trim( $text );
                }

                $truncated = mb_substr( $text, 0, $limit );
                $truncated = preg_replace( '/\s+\S*$/u', '', $truncated );

                if ( empty( $truncated ) ) {
                        $truncated = mb_substr( $text, 0, $limit );
                }

                return rtrim( $truncated ) . $ellipsis;
        }
}

namespace PF2\AI;

use RuntimeException;

/**
 * Interface for AI content adapters.
 */
interface AdapterInterface {
        /**
         * Generate AI-assisted content.
         *
         * @param array $args Prompt arguments.
         *
         * @return array{
         *     title:string,
         *     description:string,
         *     outline:array<int, string>,
         *     provider?:string
         * }
         */
        public function generate( array $args ): array;
}

/**
 * Mock adapter for deterministic local generation.
 */
class MockAdapter implements AdapterInterface {
        /**
         * {@inheritDoc}
         */
        public function generate( array $args ): array {
                $topic    = isset( $args['topic'] ) ? (string) $args['topic'] : '';
                $tone     = isset( $args['tone'] ) ? (string) $args['tone'] : 'neutral';
                $keywords = array();

                if ( isset( $args['keywords'] ) && is_array( $args['keywords'] ) ) {
                        foreach ( $args['keywords'] as $keyword ) {
                                $keyword = (string) $keyword;
                                $keyword = \sanitize_text_field( $keyword );

                                if ( '' !== $keyword ) {
                                        $keywords[] = $keyword;
                                }
                        }
                }

                $lang = isset( $args['lang'] ) ? (string) $args['lang'] : 'id';
                $lang = strtolower( $lang );

                $tone = strtolower( $tone );
                $tone = in_array( $tone, array( 'neutral', 'friendly', 'formal', 'salesy' ), true ) ? $tone : 'neutral';

                $title       = $this->build_title( $topic, $tone, $lang );
                $description = $this->build_description( $topic, $tone, $lang, $keywords );
                $outline     = $this->build_outline( $topic, $lang );

                return array(
                        'title'       => $title,
                        'description' => $description,
                        'outline'     => $outline,
                        'provider'    => 'mock',
                );
        }

        /**
         * Build a SEO-friendly title.
         *
         * @param string $topic Topic text.
         * @param string $tone  Tone keyword.
         * @param string $lang  Language code.
         *
         * @return string
         */
        protected function build_title( string $topic, string $tone, string $lang ): string {
                $topic = trim( $topic );

                if ( '' === $topic ) {
                        $topic = \__( 'Konten Tanpa Judul', 'pf2' );
                }

                $modifiers = array(
                        'id' => array(
                                'neutral'  => \__( 'Terbaru', 'pf2' ),
                                'friendly' => \__( 'Seru', 'pf2' ),
                                'formal'   => \__( 'Profesional', 'pf2' ),
                                'salesy'   => \__( 'Menarik', 'pf2' ),
                        ),
                        'en' => array(
                                'neutral'  => \__( 'Insights', 'pf2' ),
                                'friendly' => \__( 'Guide', 'pf2' ),
                                'formal'   => \__( 'Strategy', 'pf2' ),
                                'salesy'   => \__( 'Solutions', 'pf2' ),
                        ),
                );

                $language_key = array_key_exists( $lang, $modifiers ) ? $lang : 'en';
                $modifier     = $modifiers[ $language_key ][ $tone ] ?? $modifiers['en']['neutral'];

                $composed = trim( sprintf( '%s %s', $topic, $modifier ) );

                return \pf2_str_limit( $composed, 60 );
        }

        /**
         * Build a descriptive SEO-friendly meta description.
         *
         * @param string $topic    Topic text.
         * @param string $tone     Tone keyword.
         * @param string $lang     Language code.
         * @param array  $keywords Keywords array.
         *
         * @return string
         */
        protected function build_description( string $topic, string $tone, string $lang, array $keywords ): string {
                $topic = trim( $topic );
                $tone  = strtolower( $tone );
                $lang  = strtolower( $lang );

                $keyword_sentence = '';

                if ( ! empty( $keywords ) ) {
                        $keyword_sentence = sprintf(
                                $this->is_indonesian( $lang )
                                        ? /* translators: %s: comma separated keyword list. */ \__( ' Fokus pada kata kunci: %s.', 'pf2' )
                                        : /* translators: %s: comma separated keyword list. */ \__( ' Focus keywords: %s.', 'pf2' ),
                                implode( ', ', array_slice( $keywords, 0, 2 ) )
                        );
                }

                if ( $this->is_indonesian( $lang ) ) {
                        $tone_context = array(
                                'neutral'  => \__( 'yang terstruktur dan mudah dipahami', 'pf2' ),
                                'friendly' => \__( 'dengan gaya bersahabat dan menginspirasi', 'pf2' ),
                                'formal'   => \__( 'dengan pendekatan profesional dan mendalam', 'pf2' ),
                                'salesy'   => \__( 'untuk mendorong keputusan pembelian cerdas', 'pf2' ),
                        );
                        $cta_context  = array(
                                'neutral'  => \__( 'Temukan langkah praktis untuk diterapkan segera.', 'pf2' ),
                                'friendly' => \__( 'Siap membantu Anda bergerak cepat bersama tim.', 'pf2' ),
                                'formal'   => \__( 'Kuasai strategi terbaik guna memperkuat inisiatif Anda.', 'pf2' ),
                                'salesy'   => \__( 'Dorong konversi dengan narasi penjualan yang kuat.', 'pf2' ),
                        );

                        $base = sprintf(
                                /* translators: 1: topic, 2: tone narrative, 3: CTA context, 4: keyword list. */
                                \__( '%1$s menghadirkan ulasan %2$s agar Anda memahami %1$s lebih cepat. %3$s%4$s', 'pf2' ),
                                ucfirst( $topic ),
                                $tone_context[ $tone ] ?? $tone_context['neutral'],
                                $cta_context[ $tone ] ?? $cta_context['neutral'],
                                $keyword_sentence
                        );
                } else {
                        $tone_context = array(
                                'neutral'  => \__( 'with structured and accessible insights', 'pf2' ),
                                'friendly' => \__( 'with an upbeat, human tone to inspire action', 'pf2' ),
                                'formal'   => \__( 'with executive-level depth and clarity', 'pf2' ),
                                'salesy'   => \__( 'designed to drive confident purchase decisions', 'pf2' ),
                        );
                        $cta_context  = array(
                                'neutral'  => \__( 'Discover practical steps you can apply immediately.', 'pf2' ),
                                'friendly' => \__( 'Let this guide energize your next collaboration.', 'pf2' ),
                                'formal'   => \__( 'Equip your roadmap with proven, board-ready insights.', 'pf2' ),
                                'salesy'   => \__( 'Turn prospects into clients with persuasive messaging.', 'pf2' ),
                        );

                        $base = sprintf(
                                /* translators: 1: topic, 2: tone narrative, 3: CTA context, 4: keyword list. */
                                \__( '%1$s delivers %2$s so you can execute on %1$s with confidence. %3$s%4$s', 'pf2' ),
                                ucfirst( $topic ),
                                $tone_context[ $tone ] ?? $tone_context['neutral'],
                                $cta_context[ $tone ] ?? $cta_context['neutral'],
                                $keyword_sentence
                        );
                }

                $base = trim( preg_replace( '/\s+/', ' ', $base ) );

                if ( mb_strlen( $base ) < 140 ) {
                        $base = $this->pad_description( $base, $lang );
                }

                return \pf2_str_limit( $base, 160 );
        }

        /**
         * Build an outline with 5-6 bullet points.
         *
         * @param string $topic Topic text.
         * @param string $lang  Language code.
         *
         * @return array<int, string>
         */
        protected function build_outline( string $topic, string $lang ): array {
                $topic = trim( $topic );

                if ( $this->is_indonesian( $lang ) ) {
                        $outline = array(
                                sprintf( \__( 'Pendahuluan: mengapa %s penting', 'pf2' ), strtolower( $topic ) ),
                                sprintf( \__( 'Tantangan utama dalam %s', 'pf2' ), strtolower( $topic ) ),
                                sprintf( \__( 'Solusi PutraFiber untuk %s', 'pf2' ), strtolower( $topic ) ),
                                \__( 'Manfaat dan hasil yang bisa dicapai', 'pf2' ),
                                \__( 'Langkah implementasi dan studi kasus', 'pf2' ),
                                \__( 'Ajakan bertindak dan dukungan lanjutan', 'pf2' ),
                        );
                } else {
                        $outline = array(
                                sprintf( \__( 'Introduction: why %s matters', 'pf2' ), strtolower( $topic ) ),
                                sprintf( \__( 'Key challenges around %s', 'pf2' ), strtolower( $topic ) ),
                                sprintf( \__( 'PutraFiber solutions for %s', 'pf2' ), strtolower( $topic ) ),
                                \__( 'Benefits and measurable outcomes', 'pf2' ),
                                \__( 'Implementation roadmap and proof points', 'pf2' ),
                                \__( 'Call to action and next steps', 'pf2' ),
                        );
                }

                return array_map( '\sanitize_text_field', $outline );
        }

        /**
         * Determine if the language is Indonesian.
         *
         * @param string $lang Language code.
         *
         * @return bool
         */
        protected function is_indonesian( string $lang ): bool {
                return 'id' === strtolower( $lang );
        }

        /**
         * Pad description to reach minimum length.
         *
         * @param string $base Description base.
         * @param string $lang Language code.
         *
         * @return string
         */
        protected function pad_description( string $base, string $lang ): string {
                $padding = $this->is_indonesian( $lang )
                        ? \__( ' Pelajari juga rekomendasi praktis dari para ahli kami.', 'pf2' )
                        : \__( ' Explore expert-backed recommendations you can deploy today.', 'pf2' );

                return trim( $base . $padding );
        }
}

/**
 * Placeholder adapter for OpenAI integration.
 */
class OpenAIAdapter implements AdapterInterface {
        /**
         * {@inheritDoc}
         */
        public function generate( array $args ): array {
                $api_key = \pf2_options_get( 'ai_api_key', '' );

                if ( empty( $api_key ) ) {
                        $fallback          = new MockAdapter();
                        $result            = $fallback->generate( $args );
                        $result['provider'] = 'mock';

                        return $result;
                }

                throw new RuntimeException( 'OpenAI adapter not implemented yet.' );
        }
}
