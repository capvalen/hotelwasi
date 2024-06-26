import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
	{
		path: '/',
		name: 'home',
		component: HomeView
	},
	{
		path: '/clientes',
		name: 'clientes',
		component: () => import(/* webpackChunkName: "about" */ '../views/Clientes/HomeClientes.vue')
	},
	{
		path: '/habitaciones',
		name: 'habitaciones',
		component: () => import(/* webpackChunkName: "about" */ '../views/Habitaciones/HomeHabitaciones.vue')
	},
	{
		path: '/habitacion/registrar/:idHabitacion',
		name: 'registrarHabitacion',
		component: () => import(/* webpackChunkName: "about" */ '../views/Reservas/Reservaciones.vue')
	},
	{
		path: '/habitacion/reservar/:idHabitacion',
		name: 'reservarHabitacion',
		component: () => import(/* webpackChunkName: "about" */ '../views/Reservas/Reservaciones.vue')
	},
	{
		path: '/habitacion/detalle/:idHabitacion',
		name: 'detalleHabitacion',
		component: () => import(/* webpackChunkName: "about" */ '../views/Habitaciones/DetalleHabitacion.vue')
	},
	{
		path: '/habitacion/editar/:idHabitacion',
		name: 'editarHabitacion',
		component: () => import(/* webpackChunkName: "about" */ '../views/Habitaciones/EditarHabitacion.vue')
	},
	{
		path: '/reserva/ver/:idReserva',
		name: 'detalleReserva',
		component: () => import(/* webpackChunkName: "about" */ '../views/Habitaciones/DetalleHabitacion.vue')
	},
	{
		path: '/caja',
		name: 'caja',
		component: () => import('../views/Caja/HomeCaja.vue')
	},
	{
		path: '/caja/detalle/:idCaja',
		name: 'detalleCaja',
		component: () => import('../views/Caja/HomeCaja.vue')
	},
	{
		path: '/reservas',
		name: 'reservas',
		component: () => import(/* webpackChunkName: "about" */ '../views/Reservas/HomeReservas.vue')
	},
	{
		path: '/productos',
		name: 'productos',
		component: () => import(/* webpackChunkName: "about" */ '../views/Productos/HomeProductos.vue')
	},
	{
		path: '/producto/:idProducto/movimientos-stock/year/:anio',
		name: 'movimientosStock',
		component: () => import(/* webpackChunkName: "about" */ '../views/Productos/Movimientos.vue')
	},
	{
		path: '/clientes/nuevo',
		name: 'NuevoCliente',
		component: () => import(/* webpackChunkName: "about" */ '../views/Clientes/Nuevo.vue')
	},
	{
		path: '/cliente/detalle/:idCliente',
		name: 'detalleCliente',
		component: () => import(/* webpackChunkName: "about" */ '../views/Clientes/ReporteClientes.vue')
	},
	{
		path: '/reportes',
		name: 'Reportes',
		component: () => import(/* webpackChunkName: "about" */ '../views/Reportes/ReportesHome.vue')
	},
]

const router = createRouter({
	history: createWebHistory(process.env.BASE_URL),
	routes
})

export default router
