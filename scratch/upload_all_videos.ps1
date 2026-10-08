$videos = @(
    @{ local = 'storage/app/public/hotels/best-western-plus-bay-hills/videos/3_Best Western Plus Bay Hills(1).mp4'; remote = 'domains/primebooking.com.bd/prime-booking/storage/app/public/hotels/best-western-plus-bay-hills/videos/3_Best Western Plus Bay Hills(1).mp4' },
    @{ local = 'storage/app/public/hotels/seagull-hotels-ltd/videos/4_Seagull Hotels Ltd(1).mp4'; remote = 'domains/primebooking.com.bd/prime-booking/storage/app/public/hotels/seagull-hotels-ltd/videos/4_Seagull Hotels Ltd(1).mp4' },
    @{ local = 'storage/app/public/hotels/best-western-heritage/videos/5_BEST WESTERN HERITAGE.mp4'; remote = 'domains/primebooking.com.bd/prime-booking/storage/app/public/hotels/best-western-heritage/videos/5_BEST WESTERN HERITAGE.mp4' },
    @{ local = 'storage/app/public/hotels/ramada-by-wyndham-coxs-bazar/videos/6_Ramada by Wyndham Cox''s Bazar Kolatoli Beac.mp4'; remote = 'domains/primebooking.com.bd/prime-booking/storage/app/public/hotels/ramada-by-wyndham-coxs-bazar/videos/6_Ramada by Wyndham Cox''s Bazar Kolatoli Beac.mp4' },
    @{ local = 'storage/app/public/hotels/green-nature-resort-suites/videos/7_Green Nature Resort & Suites.mp4'; remote = 'domains/primebooking.com.bd/prime-booking/storage/app/public/hotels/green-nature-resort-suites/videos/7_Green Nature Resort & Suites.mp4' },
    @{ local = 'storage/app/public/hotels/hotel-windy-terrace/videos/8_Hotel Windy Terrace.mp4'; remote = 'domains/primebooking.com.bd/prime-booking/storage/app/public/hotels/hotel-windy-terrace/videos/8_Hotel Windy Terrace.mp4' },
    @{ local = 'storage/app/public/hotels/dera-resort-spa-coxs-bazar/videos/9_Dera Resort & Spa- Cox''s Bazar.mp4'; remote = 'domains/primebooking.com.bd/prime-booking/storage/app/public/hotels/dera-resort-spa-coxs-bazar/videos/9_Dera Resort & Spa- Cox''s Bazar.mp4' },
    @{ local = 'storage/app/public/hotels/praasad-paradise/videos/10_Praasad Paradise.mp4'; remote = 'domains/primebooking.com.bd/prime-booking/storage/app/public/hotels/praasad-paradise/videos/10_Praasad Paradise.mp4' }
)

foreach ($v in $videos) {
    Write-Host "Uploading $($v.local)..."
    scp $v.local "hostinger-prime:$($v.remote)"
}

Write-Host "Done uploading all videos!"
