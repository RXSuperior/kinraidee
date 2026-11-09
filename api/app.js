// app.js

// พิกัดเริ่มต้นสำรอง (กรณีผู้ใช้ไม่อนุญาตสิทธิ์ Location)
const DEFAULT_LAT = 13.2842;
const DEFAULT_LNG = 100.9234;

const statusDiv = document.getElementById('status');
let userMarker = null;
let restaurantLayer = L.layerGroup(); // กลุ่ม Layer สำหรับจัดการลบหมุดร้านเดิมเมื่ออัปเดต

// 1. สร้างวัตถุแผนที่ Leaflet
const map = L.map('map').setView([DEFAULT_LAT, DEFAULT_LNG], 14);

// 2. ดึง Layer ภาพแผนที่ฟรีจาก OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// เพิ่ม Layer กลุ่มหมุดร้านลงแผนที่
restaurantLayer.addTo(map);

// 3. ฟังก์ชันดึงพิกัด Geolocation และเริ่มต้นค้นหา
function initLocation() {
  if (!navigator.geolocation) {
    statusDiv.innerHTML = '⚠️ เบราว์เซอร์ไม่รองรับ Geolocation — แสดงตำแหน่งเริ่มต้น';
    fetchNearbyRestaurants(DEFAULT_LAT, DEFAULT_LNG);
    return;
  }

  statusDiv.innerHTML = '⏳ กำลังดึงตำแหน่งของคุณ...';

  navigator.geolocation.getCurrentPosition(
    (position) => {
      const userLat = position.coords.latitude;
      const userLng = position.coords.longitude;

      statusDiv.innerHTML = `📍 ตำแหน่งของคุณ: <b>${userLat.toFixed(4)}, ${userLng.toFixed(4)}</b>`;

      // เลื่อนจุดศูนย์กลางแผนที่ไปตำแหน่งผู้ใช้
      map.setView([userLat, userLng], 15);

      // ปักหมุดตำแหน่งผู้ใช้ (Marker สีน้ำเงินปกติ)
      if (userMarker) map.removeLayer(userMarker);
      userMarker = L.marker([userLat, userLng])
        .addTo(map)
        .bindPopup('<b>📍 คุณอยู่ที่นี่</b>')
        .openPopup();

      // โหลดข้อมูลร้านอาหารรอบพิกัดผู้ใช้
      fetchNearbyRestaurants(userLat, userLng);
    },
    (error) => {
      console.warn('Geolocation Error:', error.message);
      statusDiv.innerHTML = '⚠️ ไม่สามารถเข้าถึงตำแหน่งได้ — แสดงตำแหน่งเริ่มต้น';
      fetchNearbyRestaurants(DEFAULT_LAT, DEFAULT_LNG);
    },
    { enableHighAccuracy: true, timeout: 10000 }
  );
}

// 4. ฟังก์ชันยิง Overpass API เพื่อเอาข้อมูลร้านอาหาร
async function fetchNearbyRestaurants(lat, lng, radiusMeters = 3000) {
  statusDiv.innerHTML += ' (กำลังโหลดข้อมูลร้านอาหาร...)';

  // Overpass QL Query: ดึง Node ร้านอาหาร, คาเฟ่, ฟาสต์ฟู้ด ในรัศมีที่กำหนด
  const query = `
    [out:json][timeout:25];
    (
      node["amenity"="restaurant"](around:${radiusMeters},${lat},${lng});
      node["amenity"="cafe"](around:${radiusMeters},${lat},${lng});
      node["amenity"="fast_food"](around:${radiusMeters},${lat},${lng});
    );
    out body;
  `;

  try {
    const response = await fetch('https://overpass-api.de/api/interpreter', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'data=' + encodeURIComponent(query)
    });

    if (!response.ok) throw new Error(`HTTP Error status: ${response.status}`);

    const data = await response.json();
    renderRestaurantMarkers(data.elements);

    statusDiv.innerHTML = `✅ พบร้านอาหารทั้งหมด <b>${data.elements.length}</b> แห่ง ในรัศมี ${(radiusMeters/1000).toFixed(1)} กม.`;
  } catch (err) {
    console.error('Fetch Overpass Error:', err);
    statusDiv.innerHTML = '❌ เกิดข้อผิดพลาดในการโหลดข้อมูลจาก Overpass API';
  }
}

// 5. ฟังก์ชันวาดหมุดบน Leaflet Layer
function renderRestaurantMarkers(places) {
  // ล้างหมุดเดิมออกก่อน
  restaurantLayer.clearLayers();

  places.forEach(place => {
    const name = place.tags.name || 'ร้านอาหารไม่ระบุชื่อ';
    const cuisine = place.tags.cuisine ? `<br><b>ประเภทอาหาร:</b> ${place.tags.cuisine}` : '';
    const amenityType = place.tags.amenity || 'restaurant';

    // สร้าง Marker ร้านอาหารแล้วเพิ่มลง Layer
    const marker = L.marker([place.lat, place.lon])
      .bindPopup(`
        <div style="font-size: 14px;">
          <h4 style="margin: 0 0 5px 0; color: #2c3e50;">${name}</h4>
          <small style="color: #7f8c8d;">หมวดหมู่: ${amenityType}</small>
          ${cuisine}
        </div>
      `);

    restaurantLayer.addLayer(marker);
  });
}

// เรียกทำงานเมื่อโหลดสคริปต์
initLocation();