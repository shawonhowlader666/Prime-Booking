<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;

class SeoSchemaService
{
    /**
     * Generate Google-compliant JSON-LD schema for a hotel.
     */
    public function generateHotelSchema(Property $property): string
    {
        $schema = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Hotel',
            'name'          => $property->name,
            'description'   => strip_tags((string)($property->description ?? "Book {$property->name} in {$property->city} on PRIME BOOKING.")),
            'image'         => $property->primary_image ? [$property->primary_image] : [],
            'url'           => url('/hotels/' . ($property->slug ?: $property->id)),
            'telephone'     => $property->contact_phone ?: '+8809612345678',
            'priceRange'    => '৳ ' . number_format((float)($property->price_per_night ?? 5000)) . ' - ৳ ' . number_format((float)($property->price_per_night ?? 5000) * 3),
            'address'       => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $property->address ?: "{$property->city}, Bangladesh",
                'addressLocality' => $property->city ?: 'Dhaka',
                'addressRegion'   => $property->city ?: 'Dhaka',
                'addressCountry'  => 'BD',
            ],
            'starRating'    => [
                '@type'       => 'Rating',
                'ratingValue' => (string) ($property->star_rating ?? 4),
            ],
            'aggregateRating' => [
                '@type'       => 'AggregateRating',
                'ratingValue' => (string) number_format((float)($property->rating_score ?? 8.5), 1),
                'reviewCount' => (string) max(1, (int)($property->total_reviews ?? 10)),
                'bestRating'  => '10',
                'worstRating' => '1',
            ],
            'makesOffer'    => [
                '@type'         => 'Offer',
                'price'         => (string) ($property->price_per_night ?? 5000),
                'priceCurrency' => 'BDT',
                'availability'  => 'https://schema.org/InStock',
                'validFrom'     => date('Y-m-d'),
            ],
        ];

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
