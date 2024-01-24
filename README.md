# Automatic Flight Check-in

## TL;DR

Allows you to schedule an automated check-in to your flight, so you don't have to do it yourself. Once your reservation details (confirmation # and first and last name) are provided, the software will check you in to your flight 24 hours before it departs, with no manual steps from you.


## What is This⁈ / The Story

It all started back in early-2019 when I was planning on flying ✈ somewhere with Southwest Airlines. As most know, Southwest doesn't have assigned seating and is first come, first serve. But you still must check in to your flight up to 24 hours prior to the flight departing. The advantage of checking in early in the 24 hour period is that you get to board sooner (at least in theory), as the earlier you check in, the earlier the boarding group (A, B, C, D) and boarding group position (a single or two digit number) you receive.

**Problem:** What if your flight departs early in the morning, and you don't want to wake up early the day before to check in to your flight? Or you're simply busy and don't want to have to remember to check in 24 hours in advance?

**Solution:** AUTOMATIC CHECK-IN ❗ ❗ ❗

So, I decided to develop a web app that would automatically check you in to your Southwest Airlines flight. It took a bit of investigation to determine how the request was made, where it was made to, and what information was required to be sent with the request. I eventually figured it out, and found that a key piece of information is needed to complete the final request (to confirm the flight). A _token_ is required, which is discussed in further detail below. The API requests also require specific HTTP headers to be sent with it.


## How it Works

See [the web scheduler](#the-web-scheduler) below for how this scheduled check-in is created.

1) I discovered that when a user checked in to their flight at southwest.com, the website makes a call to an API, which you can see in `airlines/southwest.php`.

2) The first API call is to the _review_ endpoint, which returns detailed data about the flight. In order to make this request, the passengers _first_ and _last name_, and their _flight confirmation number_, as well as some miscellaneous unimportant data is sent with the request. Some of the detailed data that is returned in the response is described below.

    - **Reservation/traveler information:** all passengers and their _full name_, _confirmation number_, _type of trip_, _TSA PreCheck eligible_, and even whether the passenger is _eligible for a drink coupon_ or not.

    - **Flight information:** _departure/arrival date and time_, _flight number_, _length of flight_.

    - **And most importantly:** a <span style="color: green;">_token_</span>, which is **required** in order to make the subsequent API request (more on that in a moment).

    A brief portion of JSON returned from this endpoint is shown in the section [below](#sample-review-api-response), with some information removed or changed for privacy.

3) After receiving the response from the review endpoint, the _token_ returned with that response is necessary to make the subsequent API request to the _confirmation_ endpoint. This endpoint also returns similar JSON data that was returned from the review request, but with a couple of key differences. Among other differences, the key difference is shown below.

    - **Traveler information:** _boarding group_ and _boarding group position_.

    A brief portion of JSON returned from the confirmation endpoint is shown in the section [below](#sample-confirmationw-api-response), with some information removed or changed for privacy. You can see that the token is quite lengthy.

    This completes the check-in process of the passenger and the person has now successfully been checked in to their flight.


### The Web Scheduler

1) A user visits the check-in (`checkin.php`) page and inputs the required information to check in to their flight. This includes their confirmation #, and their first and last name.

2) The information is sent to the scheduler (`schedule.php`) and the scheduler handles the validation and creation of the cron job.

    Herein, the following occurs:

    1) A reference number is generated (was to be used in a future [feature](#ideas--improvements)).

    2) The check-in date and time is calculated and properly formatted (for creation of the cron job).

    3) The cron job entry is created and added to the crontab, so that it can be executed at the schedule time.

3) A confirmation modal is displayed to the user. The user has the option to have an email confirmation sent to them.

4) When it's time for the check-in to occur, the airline (Southwest in this case) program is executed, which is explained [above](#how-it-works).


## Sample Review API Response

<details>
  <summary>Review API Response <span style="font-size: small;">(click to expand)<span></summary>

```JSON
{
  "data": {
    "searchResults": {
      "reservation": {
        "confirmationNumber": "MVDNPS",
        "international": false,
        "seniorPassenger": false,
        "eligibleForDrinkCoupon": false,
        "upgradeToBusinessSelect": true,
        "tripType": "roundtrip",
        "travelers": [
          {
            "extraSeat": false,
            "firstName": "Johnny",
            "middleName": null,
            "lastName": "5",
            "givenName": "Johnny",
            "surname": "5",
            "boardingBounds": [
              {
                "eligibleForSelfServiceCheckIn": true,
                "boardingSegments": [
                  {
                    "tsaPreCheck": true,
                    "eligibleForDrinkCoupon": false,
                    "disabilityAssistance": false
                  }
                ]
              }
            ]
          }
        ],
        "bounds": [
          {
            "international": false,
            "segments": [
              {
                "flightIdentifier": "",
                "originationAirportCode": "MCO",
                "destinationAirportCode": "MIA",
                "departureDate": "2018-08-04T10:25-07:00",
                "flightLegs": [
                  {
                    "operatingFlightNumber": "1856",
                    "originationAirportCode": "MCO",
                    "destinationAirportCode": "MIA",
                    "departureDateTime": "2018-08-04T10:25-07:00",
                    "arrivalDateTime": "2018-08-04T11:35-07:00",
                    "changePlanes": false,
                    "nextDay": false,
                    "wifi": true,
                    "totalDuration": 60,
                    "stopDuration": 0
                  }
                ]
              }
            ],
            "type": "departing"
          }
        ]
      },
      "token": "eyJhbGciOiJkaXIiLCJlbmMiOiJBMTI4Q0JDLUhTMjU2In0..wyoMHC2oQo9nOjwRWSMSXA.cbtOFC53BeZssG9Uvp0fEvRtDpif5Jcp876KFIrjIB9ZWgqRG6fqmS69J1n_8YzWJy40iER7p9o1qfN7MFu5h6DQGsjo0zPn9vIRVzQwI738_xgn3Pq4--A2qUXB4rCG5TyJuCTaUhRNJ9wZSReGD6zOYu0viP0oIVzakqjGbM7qPdbRncrkaaeQGGEyW3-8bMunxcH6GEa9-P8b4kYXsT1RhlxqRHD5bBfGDcLIit58m10xa6ilI0I5q-M5jfRpFb0qri2ggQxNw-WTUO3hxJ_Bjf5V72Q4BJEtCxjqXaqH8924rBSkUYihrdQOtYW2TCsPoek-UjpgTePlcJITQLnUX3riIkHfYBNSr_2UW2fwm2igiTrlmmD_A7gABZq3U0gRgS37PungW5Rc93i12MvOT77tSmOri5-JvHssNDCh4d1Mhhlpm_2kDKL8SBIPw9bffaHlYtC6F8wyqoF3aIfDYeuZUcXmx7R0V6ccS4uQh-cMrXbudPffehpT6THg6JYrZSgUk8cAEmzKhvGb9TMNvsvdAmy6-Ar_mSwSgNJ3UyY-zOT32INw7chUdx1q7omCMMAFaGBeJZPnJUJ6H6WXTzDDOOyKE69cypnPBPSxo5pd9MORQlgPV6JTMiBXzZdA3HR4VBRP0AiEGDXeKfcWZCX5LDMy-sT362-6gveVusrZSUnTZsfvnn90vnz-w6tTJscir_RfkRmqehjPqOy4EmmG7TCsSeEMnRlA4O8Ez-gHufEvjNtJFze0x0K9TaNoBcL1Ce1D1P64Ntrd1ypBNo3ojZmEzWP1aHtLODKw1ZWAYI_ZcZCG84mQ-Eu6mqWldGGMSZH-LAxD4_grpe4vd-KnA5TMlI0izOQ-DKyWiANVEOSizz_tmWlPNmWkgXJRjpJ0Y0EcYQZeh0cyzTFPwy9ZTVu1NLgNjggTik6HatBjhQGEGX0bPHhWF8IJerpEYJp95HxPOjhXDqf5XX0pP_pyKH1-viA4Ze2OrEfrU6D8QRD673QmFC-uJEIQfvtL17s-pmjt5wzKbTW9mUABnLo_v2Mudbx1C_sM77Noyw2m6SBBXMzsiavCMc8fW9YlzxcLFu37-mERoavJGTRmoH74mb6qkMbtvIlWGetfoEM8wk9lZRTV6TECMzGYG04foKr48zJ5n8rRxCYmb-0mk4WPmRzve637g9jCZ3_uHktCsSfcN8VxdRVOCU2kkA-M4z8WViI1iZ6Bj8pZ2C1b-KsgfeOMvd7UoTGx6hnbkwoSVDIipIflYKz6q8SKZ22xlKEfYRO0kTtVRIcOE9oG5YyYF9jHFJk09f_DLy_GPrgLcQzJFQIWepJeYsE7yGQXpk22nXxT6cdp2jorz28cXBcAFijGNVy74TDif2oSCGAckODdxMO64JXLL50vYSuuqj2wrAVtoEfJ7LGbmXlYmcmFoypGSXW3VJ8vMsEu9HbaVXProrH9ROqBmrJANeEFiNRBAVxUIgrRjxAUWsfa7PKROSmvvHkMC_785HpYL9x7pYPDOJPPnHqwMlJ6TEL1PRT1K3xME_pKFsYoIsfUCaBCyJ2JZys6ydFJ7bWHn0rl9TA_5OuegNFuKA2KV6SCkXSMLIWHZ9WXlec_PYdUMxJmhukRZNF0dguYkq_sAhThuPZoB06ML5Xz-1bHZydkQImOOnJbfd8m1Q1cQ7ax0mRr5jPiPkwaAwG9Zn4p2sYy_HBECWrq28kX_fafVZBEzWqmHvxB8ZW0lwDyqxI6n0LChNDx7_ZSxYKXArvKeEQcjWCXlmHPlO3S3mpW4Qr-zb0Rmtffx9NLRkYOi2DWlzqWNVaZmSXJia3oo_ST5Ajie8uQa5l6zkIOLbSAG_xKL_wvAlxUr2zf-Hlx5gHPNJtsXqjLFxFmvprzLshJdizWJv93GV1lnZTFJidZ7xhgkrNapEj_mlF0z_vbrRFJ6d37WeQFlkXbI715GhHFhWwB1fvZn4LPrMwBotqfecm_JTIaUI9niqk76IvTe4xSuskoaOtxGjQgk1w_YsG3CTky05rMHb_3We1PopKO6iZnxl6LBEjXzTKUHM4IFophz7EtNp5ZBPMlDIvLHeOmWf_10LkYrSyjEFqlMV2WWlY_iBU7N8J0piFynbc5No34i1C6jJ8czRZA8ollw4JmBUSr0iwZgP0047vtn-jH0gnAgsuBWk5I4UBjA2WttQwk079WeFfJICFh_0vnMY5bpan1_g8cNpHFCtcak2MUe7qcTF75G0vy7gL5LnNkQzG3HMONGaNXIJ4ePIlJ-L0cZMrOJyPdzpaQ8yc_D5A1TJJAHXOkj6K6UsQoF_fLYg64YMRz2S0EfCgp6wLwW8kDzm5T6VrkE-bmxbaXrUs2fDR1RDU6bCghQuAxEY5QnPd3cK2Bfr4-xWHopwsDe-PQQKl3lG1u9CXRHrkUOSs7AcdUS0E8vLBE7tNrZlUvtGY8cnGCvXbfT8skcCSM0kmrSCpRj5DcxdCrzr3mORRkw-qj2O0qy6R6hs8Y4OYhkho1VqarNUCeFvlWpaepnVXMVGuGMTdOPnSiqMFYuZCWwIh7lMxqDFIvXZIo7G-ajWhv3YigUwL7yM0fJHlFczTtD6iDNwzFyzU_OT5Pl9poAm8YLVbE5hm4P0cjOF3cfkVb_CMmpVeE4J0BfqMXhZw_4PsmbFQgu4sOoPhOC1ZCjmMIrP54GSG6itBNv4xb40qXo7xyyx9DqZtbktCexrVNjfGx0_AkiT-E4-C_uDyVDh2618bnJePfcrUtOLpSNkKvn7HQGcRjlECPishXIoW8qNpm0SSbrAaqod-d30MnzfCr_RF-NxUc2Ph2uANfu7JlK1UsOu9f9JUftq8zPA36EgsXMHU6GrQ0cr9Ob6NoBgwGFoFUM7OUZ2UShXytjqq4fG0DCgvS0OBDwfY4jkC5QgXO8NEYm9QieYGnDItcfeMtiFqRb3irz7OeFhRjCO3vZkIFuQvt5ZNQMRocWLJ_AgmF5KWKLdN6cP261oDIQ7k2Hc-JsdjSi8AXPNTKwfU7ASstMgb583sTHa0y67L0QtnUk2fE0efe3WyYDOpOsPMu2J7FNkXBSfQ-fOgJnNkCbGt6SxIkygzQZdnLb98xsE0tPYLHFo-NWRkcfz2zD49if4JouDwPudyn9hiMWMfiLtLhU9Iyn3DzrgNoI9GDYPPKP68wwKKZrRx7sV4WqllHT2Jcdg7WbYg6dZtTv6KZR7ftDgqvaT-kf3u7YUoGseqvpiQxs83wRNKN2tuNAL_Yz0xJN_QAh8BWHU8bQ8oB6nuOG0ErFQ3gmBZiyIqA8mSwBifdPzzl3opnQU3hkx6kEG0mGwyB4XrN7pco5Qj5mnG5g3jI7JYK4FWz5OSTHRncXZhxJZEYv9TMgxtiFwspuV8Y6ddf2zrpdQxR5SGMc1IYwxsXZhSU0Y5QJooDpSMCmXBx0JdnR5EkJYn4JCwQhT9BOxrQIAXCsIRp6CbdxRcoJmV61hK3ZBKAwB1YxUw_pUEAAmceO45tkTmJhDAZY5ii50QOrlDzbHQVdWE_WVDpDrthf27aIYqBr3dk1t88RIEj2cSJEgdE_l0mflbY-2xVS1K81mGeSZZVBH5FcjkQGv7qYKEx8Ah-6YFxHts7dpdFQNK1FY6KYjpEaxVRIu-ctV4V9yLp3_-Xg_B82Br4a7W5ADSeL5N6hHim8L_1yDF1aOSMrdEpopnRBattdznfgF9TsIFf1XsmmpicNi-grLsiXB6ZGZ-s3dqfs8V8qPybv4gPhTvhtpyYiQvrozQyK0UMCAlJHbuk7TmsZJobmPesJIDDFU0k_k5CkT1ZsIbwU-NHC3WtA22bj9Un1BotvlMx0ItixPdNKMO5YjpHeHGJM-zDWWt7BmQJ40S-g-6PtEMw5CkMNdHVlT6XZ9WADjgI5h1zIB9YHcjj4wlfkVdaVZE0IBd6QtYGpBH7qpO0gyEJ72OiOgizhwkhpnabCIs1flp4gao1_6eDCjn9UznEtc455qvNit5g3pdkXMRuTBuFghq8uHONfvG2zqJqQ9eY8iog1BQ2lwvdLfSfKz4iGSsISZQcd19lAEj6CU9ICT3cQMyZm36J1Z7jsCNf9m7CojhH5tpG2WiBDGhe-BGhIVFGNkftVDfBrOEFUdQFZHm5hNiwQGyTJQ2zswxVK4BTzmDwdofppLObj2Q2JUfMeZx68RY5kSEMhlC4BTtJkxYtN1cple6u23x1RX4EXMlzKPr-qox6ohdI9sj-L8JWFKE7H2N-xQjrGpEtAeg903rg2QL7UyfhAFOVyFwc-Fxfs1RNvwIV-ynjhQh58dmzMSF7zjEZ1DA.pmFTN1UJx2Hro0NYrOoGjQ"
    }
  },
  "success": true
}

```
</details>


## Sample Confirmation API Response

<details>
  <summary>Confirmation API Response <span style="font-size: small;">(click to expand)<span></summary>

```JSON
{
  "data": {
    "searchResults": {
      "travelers": [
        {
          "boardingBounds": [
            {
              "eligibleForSelfServiceCheckIn": false,
              "boardingSegments": [
                {
                  "boardingGroup": "B",
                  "boardingGroupPosition": "38",
                  "tsaPreCheck": true,
                  "eligibleForDrinkCoupon": false
                }
              ]
            }
          ]
        }
      ],
      "eligibleForMobileBoardingPass": true,
      "token": "[REMOVED FOR BREVITY]",
      "drinkCouponSelected": false
    }
  },
  "success": true,
  "notifications": null,
  "uiMetadata": {
    "proxyLogout": true,
    "chapiVersion": "1.8.0",
    "maintenance": false,
    "group-checkin": true
  }
}
```
</details>


## The takeaway...

I encountered a lot of obstacles during the investigation and implementation of this project. But that was a good thing because I learned so much from this undertaking. The obstacles I encountered and skills I acquired are discussed below.

**Skills acquired**

1) JSON - At this point in time, I had never really worked with JSON — I knew what it was, I just didn't fully understand what purpose it served and why, when, and how to use it.

2) Curl - I had used Curl in the past on the command line, but never in code or an application such as this. As I was most familiar with PHP at the time, I chose this route.

3) Improved logical thinking - I improved my logical thinking skill, which are quite valuable in this industry.

**Obstacles encountered**

1) How do I hide who I am so Southwest Airlines doesn't just block my IP address?

    At first, the requests were sent from my Linux server at home and it hadn't even occurred to me to obfuscate my IP address. Luckily, they never blocked my IP address. Had I opened this up to the public, and provided this as a SaaS product, that may have changed. One answer to this was to use a proxy.

    I talk about this in a little more detail down in the improvements section, however, I'll touch on it here. I tried implementing this, but for possibly many reasons, I was unable to complete it and/or get it to work properly. Though, with my knowledge now, I could probably do just that.

2) For improved automated check-in timing, how do I fine-grain the automatic check-in so it runs at 23 hours, 59 minutes, 58 seconds before the flight?

    As I stated earlier, the cron job caries out the task of automatically checking the passenger in to their flight. A limitation with using cron is the inability to run a job down to the seconds level; the smallest interval of time allowed is minutes. Fifty-nine seconds is so miniscule, but it _could_ make the difference between getting a boarding group of A rather than B. However, there are lots of factors here to consider.

    The reason running the check-in at 23 hours, 59 minutes before the flight was to account for possible slight time differences between my server and Southwest's servers. However unlikely, the time could very well be 2-3 seconds different. Even a difference of one second could cause the automatic check-in to fail. If the request fails, one option is to have the request executed again, but then multiple requests to Southwest's servers would be made, for the sole purpose of checking in one passenger (and I'd rather not).

    Although I never tried implementing something that would allow the check-in to take place with such preciseness, I did do a little research/thinking. These are/were some possible options.

    1) systemd timers.

    2) Writing my own Linux daemon that would run in the background and monitor a list of check-in times and execute the check-in at that exact second.

    3) There are some open source projects I found that _may_ work.

    Something else to consider is that running tasks at a very precise time down to the seconds, doesn't necessarily mean it'll run exactly at that specified second (for example, 23:59:58).


## Ideas / Improvements
<span style="font-size: small;">(that never really materialized🙃)</span>

* **PHP object-oriented programming**<br>
I originally wrote the quickly and just wanted it to work. OOP was never a priority. But as I started thinking about how I could possibly offer this a public software service, I realized it was probably best to rewrite it in OOP code. Or perhaps writing it in a different language. I did start down this path, as you can see in the [flight-check-in beta](https://github.com/jdstone/flight-check-in/pull/1) pull request. But the code is just a terrible mess, and I have no major reason to improve/fix it.

* **Use an anonymous proxy to send the request**<br>
In the beta I created, you can see bits and pieces of a proxy implementation — the code is a terrible mess. Of course, the airline [Southwest] could have just changed their API or restrict access to it, which is what appears they eventually did. I don't remember exactly when their API was locked down from public use, but it was sometime after March 2022. As of January 2024, I haven't looked nor tried to figure out if there is a way around it.

* **Modify and/or delete the scheduled automatic check-in**<br>
A very small portion of this feature was started, and included in the beta, but unfortunately, was never completed.

* **Send email confirmation after the automatic check-in occurs**<br>
This was a feature that was included in the beta that I started, but as I stated earlier, I never completed the redesign/re-work/improvements.

* **Support for more airlines**<br>
Adding support for other airlines was planned, but that isn't happening! 😂 Since other airlines allow you to choose your seat, it would be more difficult, if not impossible.

