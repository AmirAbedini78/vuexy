<template>
  <div>
    <header class="custom-navbar">
      <div class="navbar-title">Registration Activity</div>
      <button class="logout-btn" @click="logout">Logout</button>
    </header>
    <div class="pa-8">
      <!-- صفحه خالی -->
    </div>
  </div>
</template>

<script setup>
definePage({
  meta: {
    layout: "blank", // بدون منو
    public: true,
  },
});

const logout = () => {
  // اگر logout دارید اینجا فراخوانی کنید
  // مثلا پاک کردن کوکی و ریدایرکت به لاگین
  window.location.href = "/login";
};
</script>

<style scoped>
.custom-navbar {
  width: 100%;
  height: 56px;
  background: #fff;
  border-bottom: 1px solid #eee;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px;
  font-weight: bold;
  font-size: 18px;
}
.logout-btn {
  background: #f44336;
  color: #fff;
  border: none;
  border-radius: 4px;
  padding: 6px 16px;
  cursor: pointer;
}
</style>
