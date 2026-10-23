# Crown Carz — Mobile App Advertisement Integration Guide (Flutter)

> **Document Version:** 1.0.0  
> **Target App:** Crown Carz Customer / Passenger Mobile App (iOS & Android)  
> **Backend Integration:** REST API & Firebase Realtime Database  
> **Brand Colors:** Crown Gold (`#E6B04A`), Dark Navy/Slate (`#0F172A`, `#1E293B`), Pure White (`#FFFFFF`)

---

## 1. Executive Summary

This document specifies the exact contract, data models, endpoints, and Flutter Dart implementations required to integrate live advertisements into the Crown Carz Customer Mobile App.

The backend admin panel allows admins to dynamically publish, schedule, prioritize, activate, and deactivate promotional banners and launch pop-ups without requiring mobile app store updates.

### Supported Display Placements
1. **`home_banner`**: Top or mid-screen auto-scrolling carousel on the Home Screen.
2. **`popup_modal`**: Launch pop-up dialog that appears when the app opens.
3. **`booking_screen`**: Promotional banner shown during ride confirmation or booking flow.

### Supported Click Actions
- **`in_app_screen`**: Navigates to an internal screen (e.g., `booking`, `fleet`, `wallet`).
- **`external_url`**: Opens a link in the mobile web browser.
- **`call_phone`**: Opens the phone dialer with a customer service / dispatch number.
- **`none`**: Display-only informational / branding banner with no tap redirect.

---

## 2. API Endpoints Specification

### 2.1. Fetch Active Advertisements
Fetches all currently active advertisements that have started and not expired.

- **URL:** `GET /api/advertisements/active`
- **Headers:** `Accept: application/json`
- **Query Parameters:**
  | Parameter | Type | Required | Description | Example |
  | :--- | :--- | :--- | :--- | :--- |
  | `placement` | `string` | Optional | Filter by placement (`home_banner`, `popup_modal`, `booking_screen`). Leave empty for all placements. | `home_banner` |
  | `audience` | `string` | Optional | Always default to `customer`. | `customer` |

#### Example Request:
```http
GET https://your-admin-domain.com/api/advertisements/active?placement=home_banner HTTP/1.1
Accept: application/json
```

#### Example 200 OK Response:
```json
{
  "status": true,
  "message": "Advertisements fetched successfully",
  "count": 2,
  "data": [
    {
      "id": "ad_sample_airport_01",
      "title": "Heathrow & Gatwick Transfers",
      "subtitle": "Pre-book your luxury airport transfer today. Fixed prices guaranteed.",
      "image_url": "https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=1200&q=80",
      "target_audience": "customer",
      "placement": "home_banner",
      "action_type": "in_app_screen",
      "action_value": "booking",
      "button_text": "Book Now",
      "badge_text": "VIP LUXURY",
      "priority": 1,
      "status": "Active",
      "start_date": "2026-10-23",
      "end_date": "2027-01-23",
      "clicks_count": 42,
      "views_count": 318,
      "created_at": "2026-10-21 08:52:08"
    },
    {
      "id": "ad_sample_vip_03",
      "title": "Executive Mercedes E-Class & S-Class Fleet",
      "subtitle": "Corporate travel, VIP events, and wedding chauffeur services tailored to perfection.",
      "image_url": "https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=1200&q=80",
      "target_audience": "customer",
      "placement": "popup_modal",
      "action_type": "call_phone",
      "action_value": "+441189474747",
      "button_text": "Call To Book",
      "badge_text": "HOT",
      "priority": 2,
      "status": "Active",
      "start_date": "2026-10-23",
      "end_date": "2027-10-23",
      "clicks_count": 77,
      "views_count": 540,
      "created_at": "2026-10-23 08:52:08"
    }
  ]
}
```

---

### 2.2. Track Ad View (Impression)
Call this endpoint once when an ad becomes visible on the screen.

- **URL:** `POST /api/advertisements/{id}/view`
- **Headers:** `Accept: application/json`
- **Body:** None

#### Example Response:
```json
{
  "status": true,
  "message": "View tracked successfully",
  "views_count": 319
}
```

---

### 2.3. Track Ad Click
Call this endpoint when the user taps on the ad banner or button.

- **URL:** `POST /api/advertisements/{id}/click`
- **Headers:** `Accept: application/json`
- **Body:** None

#### Example Response:
```json
{
  "status": true,
  "message": "Click tracked successfully",
  "clicks_count": 43
}
```

---

## 3. Alternative: Firebase Realtime Database Structure
If your app directly consumes Firebase Realtime Database, advertisements are synchronized under the `advertisements` root node:

```
root
 └── advertisements
      ├── ad_sample_airport_01
      │    ├── id: "ad_sample_airport_01"
      │    ├── title: "Heathrow & Gatwick Transfers"
      │    ├── subtitle: "Pre-book your luxury airport transfer today."
      │    ├── image_url: "https://..."
      │    ├── placement: "home_banner"
      │    ├── target_audience: "customer"
      │    ├── action_type: "in_app_screen"
      │    ├── action_value: "booking"
      │    ├── button_text: "Book Now"
      │    ├── badge_text: "HOT OFFER"
      │    ├── priority: 1
      │    ├── status: "Active"
      │    ├── start_date: "2026-10-23"
      │    └── end_date: "2027-01-23"
      └── ...
```

---

## 4. Flutter Implementation Code

### 4.1. Model Class: `lib/models/advertisement_model.dart`

```dart
class AdvertisementModel {
  final String id;
  final String title;
  final String subtitle;
  final String imageUrl;
  final String placement;
  final String actionType;
  final String actionValue;
  final String buttonText;
  final String badgeText;
  final int priority;
  final String status;

  AdvertisementModel({
    required this.id,
    required this.title,
    required this.subtitle,
    required this.imageUrl,
    required this.placement,
    required this.actionType,
    required this.actionValue,
    required this.buttonText,
    required this.badgeText,
    required this.priority,
    required this.status,
  });

  factory AdvertisementModel.fromJson(Map<String, dynamic> json) {
    return AdvertisementModel(
      id: json['id']?.toString() ?? '',
      title: json['title']?.toString() ?? '',
      subtitle: json['subtitle']?.toString() ?? '',
      imageUrl: json['image_url']?.toString() ?? '',
      placement: json['placement']?.toString() ?? 'home_banner',
      actionType: json['action_type']?.toString() ?? 'none',
      actionValue: json['action_value']?.toString() ?? '',
      buttonText: json['button_text']?.toString() ?? 'Learn More',
      badgeText: json['badge_text']?.toString() ?? '',
      priority: json['priority'] is int
          ? json['priority']
          : int.tryParse(json['priority']?.toString() ?? '1') ?? 1,
      status: json['status']?.toString() ?? 'Active',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'subtitle': subtitle,
      'image_url': imageUrl,
      'placement': placement,
      'action_type': actionType,
      'action_value': actionValue,
      'button_text': buttonText,
      'badge_text': badgeText,
      'priority': priority,
      'status': status,
    };
  }
}
```

---

### 4.2. API Service: `lib/services/advertisement_service.dart`

```dart
import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../models/advertisement_model.dart';

class AdvertisementService {
  // Update with your production/staging backend domain:
  static const String baseUrl = 'https://your-admin-domain.com/api';

  /// Fetches active advertisements filtered by placement
  static Future<List<AdvertisementModel>> fetchActiveAds({
    String placement = 'home_banner',
  }) async {
    try {
      final uri = Uri.parse('$baseUrl/advertisements/active?placement=$placement');
      final response = await http.get(
        uri,
        headers: {
          'Accept': 'application/json',
        },
      ).timeout(const Duration(seconds: 10));

      if (response.statusCode == 200) {
        final Map<String, dynamic> decoded = json.decode(response.body);
        if (decoded['status'] == true && decoded['data'] is List) {
          final List list = decoded['data'];
          return list.map((item) => AdvertisementModel.fromJson(item)).toList();
        }
      }
    } catch (e) {
      if (kDebugMode) {
        print('Error fetching ads ($placement): $e');
      }
    }
    return [];
  }

  /// Track when an ad is clicked
  static Future<void> trackClick(String adId) async {
    try {
      final uri = Uri.parse('$baseUrl/advertisements/$adId/click');
      await http.post(uri).timeout(const Duration(seconds: 5));
    } catch (_) {}
  }

  /// Track when an ad is viewed
  static Future<void> trackView(String adId) async {
    try {
      final uri = Uri.parse('$baseUrl/advertisements/$adId/view');
      await http.post(uri).timeout(const Duration(seconds: 5));
    } catch (_) {}
  }
}
```

---

### 4.3. Tap / Click Handler: `lib/utils/ad_click_handler.dart`

```dart
import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../models/advertisement_model.dart';
import '../services/advertisement_service.dart';

void handleAdvertisementTap(BuildContext context, AdvertisementModel ad) {
  // 1. Report click analytics to backend
  AdvertisementService.trackClick(ad.id);

  // 2. Perform the target action
  switch (ad.actionType) {
    case 'in_app_screen':
      if (ad.actionValue.isNotEmpty) {
        // Example: 'booking', 'fleet', 'profile', 'contact'
        Navigator.pushNamed(context, '/${ad.actionValue}');
      }
      break;

    case 'external_url':
      if (ad.actionValue.isNotEmpty) {
        final uri = Uri.tryParse(ad.actionValue);
        if (uri != null) {
          launchUrl(uri, mode: LaunchMode.externalApplication);
        }
      }
      break;

    case 'call_phone':
      if (ad.actionValue.isNotEmpty) {
        final phoneUri = Uri.parse('tel:${ad.actionValue.replaceAll(' ', '')}');
        launchUrl(phoneUri);
      }
      break;

    case 'none':
    default:
      // Informational / Branding display only. No action needed.
      break;
  }
}
```

---

### 4.4. Home Screen Carousel Widget: `lib/widgets/ad_banner_slider.dart`

```dart
import 'dart:async';
import 'package:flutter/material.dart';
import '../models/advertisement_model.dart';
import '../services/advertisement_service.dart';
import '../utils/ad_click_handler.dart';

class AdBannerSlider extends StatefulWidget {
  final List<AdvertisementModel> ads;
  final double height;

  const AdBannerSlider({
    Key? key,
    required this.ads,
    this.height = 160.0,
  }) : super(key: key);

  @override
  State<AdBannerSlider> createState() => _AdBannerSliderState();
}

class _AdBannerSliderState extends State<AdBannerSlider> {
  late final PageController _controller;
  int _currentIndex = 0;
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    _controller = PageController();

    // Auto-scroll every 5 seconds if there are multiple ads
    if (widget.ads.length > 1) {
      _timer = Timer.periodic(const Duration(seconds: 5), (_) {
        if (_controller.hasClients) {
          final nextIndex = (_currentIndex + 1) % widget.ads.length;
          _controller.animateToPage(
            nextIndex,
            duration: const Duration(milliseconds: 450),
            curve: Curves.easeInOut,
          );
        }
      });
    }

    // Track view for initial ad
    if (widget.ads.isNotEmpty) {
      AdvertisementService.trackView(widget.ads.first.id);
    }
  }

  @override
  void dispose() {
    _timer?.cancel();
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (widget.ads.isEmpty) return const SizedBox.shrink();

    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        SizedBox(
          height: widget.height,
          child: PageView.builder(
            controller: _controller,
            itemCount: widget.ads.length,
            onPageChanged: (index) {
              setState(() => _currentIndex = index);
              AdvertisementService.trackView(widget.ads[index].id);
            },
            itemBuilder: (context, index) {
              final ad = widget.ads[index];
              return _buildAdCard(ad);
            },
          ),
        ),
        if (widget.ads.length > 1) ...[
          const SizedBox(height: 8),
          _buildIndicatorDots(),
        ],
      ],
    );
  }

  Widget _buildAdCard(AdvertisementModel ad) {
    return GestureDetector(
      onTap: () => handleAdvertisementTap(context, ad),
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.25),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(16),
          child: Stack(
            fit: StackFit.expand,
            children: [
              // Banner Image
              Image.network(
                ad.imageUrl,
                fit: BoxFit.cover,
                errorBuilder: (_, __, ___) => Container(
                  color: const Color(0xFF1E293B),
                  alignment: Alignment.center,
                  child: const Icon(
                    Icons.directions_car,
                    color: Color(0xFFE6B04A),
                    size: 48,
                  ),
                ),
              ),

              // Gradient Overlay for Title Clarity
              Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      Colors.transparent,
                      Colors.black.withOpacity(0.85),
                    ],
                  ),
                ),
              ),

              // Highlight Badge (e.g. VIP LUXURY, NEW)
              if (ad.badgeText.isNotEmpty)
                Positioned(
                  top: 12,
                  left: 12,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(
                      color: const Color(0xFFE6B04A),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Text(
                      ad.badgeText.toUpperCase(),
                      style: const TextStyle(
                        color: Color(0xFF111827),
                        fontSize: 9.5,
                        fontWeight: FontWeight.w800,
                        letterSpacing: 0.5,
                      ),
                    ),
                  ),
                ),

              // Bottom Title, Subtitle and CTA
              Positioned(
                left: 14,
                right: 14,
                bottom: 12,
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(
                            ad.title,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 13.5,
                              fontWeight: FontWeight.bold,
                              letterSpacing: -0.2,
                            ),
                          ),
                          if (ad.subtitle.isNotEmpty) ...[
                            const SizedBox(height: 2),
                            Text(
                              ad.subtitle,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: TextStyle(
                                color: Colors.white.withOpacity(0.8),
                                fontSize: 11,
                              ),
                            ),
                          ],
                        ],
                      ),
                    ),
                    if (ad.actionType != 'none') ...[
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: const Color(0xFFE6B04A),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          ad.buttonText,
                          style: const TextStyle(
                            color: Color(0xFF111827),
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ],
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildIndicatorDots() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: List.generate(
        widget.ads.length,
        (i) => AnimatedContainer(
          duration: const Duration(milliseconds: 300),
          width: _currentIndex == i ? 18 : 6,
          height: 6,
          margin: const EdgeInsets.symmetric(horizontal: 2.5),
          decoration: BoxDecoration(
            color: _currentIndex == i
                ? const Color(0xFFE6B04A)
                : Colors.white.withOpacity(0.3),
            borderRadius: BorderRadius.circular(3),
          ),
        ),
      ),
    );
  }
}
```

---

### 4.5. Launch Pop-up Dialog Helper: `lib/widgets/ad_popup_dialog.dart`

Call `showAdvertisementPopupIfAvailable(context)` during app start or on your home screen's `initState`:

```dart
import 'package:flutter/material.dart';
import '../models/advertisement_model.dart';
import '../services/advertisement_service.dart';
import '../utils/ad_click_handler.dart';

Future<void> showAdvertisementPopupIfAvailable(BuildContext context) async {
  final popups = await AdvertisementService.fetchActiveAds(placement: 'popup_modal');
  if (popups.isEmpty || !context.mounted) return;

  final ad = popups.first;
  AdvertisementService.trackView(ad.id);

  showDialog(
    context: context,
    barrierDismissible: true,
    builder: (dialogContext) {
      return Dialog(
        backgroundColor: Colors.transparent,
        insetPadding: const EdgeInsets.symmetric(horizontal: 24),
        child: Container(
          decoration: BoxDecoration(
            color: const Color(0xFF0F172A), // Dark slate
            borderRadius: BorderRadius.circular(20),
            border: Border.all(
              color: const Color(0xFFE6B04A).withOpacity(0.8),
              width: 1.5,
            ),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Image Top Section
              Stack(
                children: [
                  ClipRRect(
                    borderRadius: const BorderRadius.vertical(top: Radius.circular(18)),
                    child: Image.network(
                      ad.imageUrl,
                      height: 190,
                      width: double.infinity,
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => Container(
                        height: 190,
                        color: const Color(0xFF1E293B),
                        child: const Icon(
                          Icons.campaign_rounded,
                          color: Color(0xFFE6B04A),
                          size: 54,
                        ),
                      ),
                    ),
                  ),
                  Positioned(
                    top: 10,
                    right: 10,
                    child: GestureDetector(
                      onTap: () => Navigator.of(dialogContext).pop(),
                      child: Container(
                        padding: const EdgeInsets.all(6),
                        decoration: BoxDecoration(
                          color: Colors.black.withOpacity(0.6),
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(Icons.close, color: Colors.white, size: 18),
                      ),
                    ),
                  ),
                  if (ad.badgeText.isNotEmpty)
                    Positioned(
                      top: 12,
                      left: 12,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: const Color(0xFFE6B04A),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          ad.badgeText,
                          style: const TextStyle(
                            color: Colors.black,
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                ],
              ),

              // Content Body
              Padding(
                padding: const EdgeInsets.fromLTRB(18, 16, 18, 18),
                child: Column(
                  children: [
                    Text(
                      ad.title,
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    if (ad.subtitle.isNotEmpty) ...[
                      const SizedBox(height: 6),
                      Text(
                        ad.subtitle,
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          color: Colors.white.withOpacity(0.75),
                          fontSize: 12.5,
                        ),
                      ),
                    ],
                    const SizedBox(height: 18),
                    SizedBox(
                      width: double.infinity,
                      height: 44,
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFFE6B04A),
                          foregroundColor: const Color(0xFF111827),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(10),
                          ),
                          elevation: 0,
                        ),
                        onPressed: () {
                          Navigator.of(dialogContext).pop();
                          handleAdvertisementTap(context, ad);
                        },
                        child: Text(
                          ad.buttonText,
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      );
    },
  );
}
```

---

## 5. Integration in Home Screen Example

```dart
class CustomerHomeScreen extends StatefulWidget {
  const CustomerHomeScreen({Key? key}) : super(key: key);

  @override
  State<CustomerHomeScreen> createState() => _CustomerHomeScreenState();
}

class _CustomerHomeScreenState extends State<CustomerHomeScreen> {
  List<AdvertisementModel> _homeBanners = [];
  bool _isLoadingAds = true;

  @override
  void initState() {
    super.initState();
    _loadAdvertisements();
  }

  Future<void> _loadAdvertisements() async {
    // 1. Fetch home carousel banners
    final banners = await AdvertisementService.fetchActiveAds(placement: 'home_banner');
    if (mounted) {
      setState(() {
        _homeBanners = banners;
        _isLoadingAds = false;
      });

      // 2. Check and trigger launch popup if configured
      showAdvertisementPopupIfAvailable(context);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0F172A),
      body: SafeArea(
        child: ListView(
          children: [
            // Your Header / Location selector...
            const SizedBox(height: 16),

            // Crown Carz Dynamic Ad Banner Carousel
            if (!_isLoadingAds && _homeBanners.isNotEmpty)
              AdBannerSlider(ads: _homeBanners)
            else if (_isLoadingAds)
              const SizedBox(
                height: 160,
                child: Center(
                  child: CircularProgressIndicator(color: Color(0xFFE6B04A)),
                ),
              ),

            // Rest of your booking options (Quick book, fleet selection, etc.)
          ],
        ),
      ),
    );
  }
}
```

---

## 6. QA & Testing Checklist

1. **Verify Live Response via cURL**:
   ```bash
   curl -s "https://your-admin-domain.com/api/advertisements/active?placement=home_banner"
   ```
2. **Click Tracking Test**:
   ```bash
   curl -X POST "https://your-admin-domain.com/api/advertisements/ad_sample_airport_01/click"
   ```
3. **Admin Panel Verification**:
   - Open Web Admin Panel -> **Advertisements**.
   - Verify that click and impression counts increment in real-time.
   - Deactivate an ad and ensure it disappears from the mobile response immediately.
