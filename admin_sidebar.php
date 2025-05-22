<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<aside class="w-64 bg-gray-100 border-r min-h-screen shadow-sm">
  <div class="p-6">
    <h2 class="text-xl font-bold text-gray-700 mb-6">Admin Panel</h2>
    <nav class="space-y-2">
      <a href="admin_dashboard.php"
         class="flex items-center gap-3 p-3 rounded-lg font-medium text-gray-700 hover:bg-blue-200 transition">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4V3H3v7zM10 21h4v-7h-4v7zM17 14h4v-4h-4v4z"/>
        </svg>
        Dashboard
      </a>

      <a href="productadmin.php"
         class="flex items-center gap-3 p-3 rounded-lg font-medium text-gray-700 hover:bg-gray-200 transition">
        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V9a2 2 0 00-2-2h-1.34a2 2 0 00-1.32-.6L13 4h-2l-.34.4A2 2 0 009.34 7H8a2 2 0 00-2 2v4M4 17h16M4 17a4 4 0 01-4-4h24a4 4 0 01-4 4"/>
        </svg>
        Products
      </a>
     <a href="add_categories.php"
   class="flex items-center gap-3 p-3 rounded-lg font-medium text-gray-700 hover:bg-gray-200 transition">
  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
  </svg>
  Categories
</a>



      <a href="admin_settings.php"
         class="flex items-center gap-3 p-3 rounded-lg font-medium text-gray-700 hover:bg-gray-200 transition">
        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 3.058-1.756 3.484 0a1.724 1.724 0 002.591 1.042c1.517-.962 3.451.972 2.49 2.49a1.724 1.724 0 001.042 2.591c1.756.426 1.756 3.058 0 3.484a1.724 1.724 0 00-1.042 2.591c.962 1.517-.972 3.451-2.49 2.49a1.724 1.724 0 00-2.591 1.042c-.426 1.756-3.058 1.756-3.484 0a1.724 1.724 0 00-2.591-1.042c-1.517.962-3.451-.972-2.49-2.49a1.724 1.724 0 00-1.042-2.591c-1.756-.426-1.756-3.058 0-3.484a1.724 1.724 0 001.042-2.591c-.962-1.517.972-3.451 2.49-2.49a1.724 1.724 0 002.591-1.042z"/>
        </svg>
        Settings
      </a>

      <a href="logout.php"
         class="flex items-center gap-3 p-3 rounded-lg font-medium text-gray-700 hover:bg-red-100 transition">
        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H5a2 2 0 01-2-2V7a2 2 0 012-2h5a3 3 0 013 3v1"/>
        </svg>
        Logout
      </a>
    </nav>
  </div>
</aside>

</body>
</html>
