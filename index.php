<?php
session_start();

// Inicializar el carrito si no existe
if (!isset($_SESSION['productos'])) {
    $_SESSION['productos'] = [];
}

// Productos predefinidos con precios
$productos_disponibles = [
    ['nombre' => 'Cámara con Audio Pudahuel', 'precio' => 17000],
    ['nombre' => 'Cámara Full HD INTCOMEX', 'precio' => 11800],
    ['nombre' => 'Cámara Full HD Pudahuel', 'precio' => 14000],
    ['nombre' => 'Cámara 1MP Dahua SSTT', 'precio' => 8900],
    ['nombre' => 'Balum INTCOMEX', 'precio' => 1890],
    ['nombre' => 'Balum HIKVISION Pudahuel', 'precio' => 2500],
    ['nombre' => 'Balum Económico Pudahuel', 'precio' => 2000],
    ['nombre' => 'Transformador 12V 2mha Pudahuel', 'precio' => 6000],
    ['nombre' => 'Transformador 12V 1.5mha SSTT', 'precio' => 3200],
    ['nombre' => 'Caja Estanca Sodimac', 'precio' => 1050],
    ['nombre' => 'Caja Estanca San Francisco', 'precio' => 1500],
    ['nombre' => 'DVR 4 Canales', 'precio' => 28000],
    ['nombre' => 'DVR 8 Canales', 'precio' => 35000],
    ['nombre' => 'Disco Duro 500GB', 'precio' => 5000],
    ['nombre' => 'Disco Duro 1TB', 'precio' => 10000],
    ['nombre' => 'Monitor VGA', 'precio' => 15000],
];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar'])) {
        $producto_seleccionado = intval($_POST['producto']);
        $cantidad = intval($_POST['cantidad']);
        
        if (isset($productos_disponibles[$producto_seleccionado]) && $cantidad > 0) {
            $prod = $productos_disponibles[$producto_seleccionado];
            $_SESSION['productos'][] = [
                'nombre' => $prod['nombre'],
                'precio' => $prod['precio'],
                'cantidad' => $cantidad,
                'subtotal' => $prod['precio'] * $cantidad
            ];
        }
    } elseif (isset($_POST['actualizar'])) {
        $indice = intval($_POST['indice']);
        $nueva_cantidad = intval($_POST['nueva_cantidad']);
        
        if (isset($_SESSION['productos'][$indice]) && $nueva_cantidad > 0) {
            $_SESSION['productos'][$indice]['cantidad'] = $nueva_cantidad;
            $_SESSION['productos'][$indice]['subtotal'] = $_SESSION['productos'][$indice]['precio'] * $nueva_cantidad;
        }
    } elseif (isset($_POST['eliminar'])) {
        $indice = intval($_POST['indice']);
        if (isset($_SESSION['productos'][$indice])) {
            unset($_SESSION['productos'][$indice]);
            $_SESSION['productos'] = array_values($_SESSION['productos']);
        }
    } elseif (isset($_POST['limpiar'])) {
        $_SESSION['productos'] = [];
        $_SESSION['precio_venta'] = 0;
    }
}

// Inicializar precio de venta
if (!isset($_SESSION['precio_venta'])) {
    $_SESSION['precio_venta'] = 0;
}

// Procesar precio de venta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calcular_ganancia'])) {
    $_SESSION['precio_venta'] = floatval($_POST['precio_venta']);
}

// Calcular total
$total = 0;
foreach ($_SESSION['productos'] as $prod) {
    $total += $prod['subtotal'];
}

// Calcular ganancia
$ganancia = $_SESSION['precio_venta'] - $total;
$porcentaje_ganancia = $total > 0 ? ($ganancia / $total) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presupuesto Cámaras de Seguridad</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .form-section h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 600;
        }
        
        select,
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
            background-color: white;
        }
        
        select:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        select {
            cursor: pointer;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            font-size: 14px;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
            padding: 8px 15px;
            font-size: 14px;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .productos-list {
            margin-top: 30px;
        }
        
        .productos-list h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 22px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        thead {
            background: #667eea;
            color: white;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
        }
        
        th {
            font-weight: 600;
        }
        
        tbody tr {
            border-bottom: 1px solid #dee2e6;
            transition: background-color 0.3s;
        }
        
        tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .precio {
            font-weight: 600;
            color: #28a745;
        }
        
        .total-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: right;
            margin-top: 20px;
        }
        
        .total-section h3 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .total-amount {
            font-size: 36px;
            font-weight: bold;
        }
        
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-size: 18px;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .ganancia-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-top: 30px;
        }
        
        .ganancia-section h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 22px;
        }
        
        .ganancia-form {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 15px;
            align-items: end;
            margin-bottom: 20px;
        }
        
        .resultado-ganancia {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
        }
        
        .resultado-ganancia.perdida {
            border-left-color: #dc3545;
        }
        
        .ganancia-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .ganancia-item:last-child {
            border-bottom: none;
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            margin-top: 10px;
        }
        
        .ganancia-item.perdida-text {
            color: #dc3545;
        }
        
        .ganancia-label {
            color: #6c757d;
        }
        
        .ganancia-valor {
            font-weight: 600;
            color: #333;
        }
        
        .porcentaje {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            margin-left: 10px;
        }
        
        .porcentaje.negativo {
            background: #dc3545;
        }
        
        .cantidad-edit {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        
        .cantidad-edit input {
            width: 60px;
            padding: 5px;
            border: 2px solid #ddd;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
        }
        
        .cantidad-edit input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-group {
            display: flex;
            gap: 5px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 15px;
            align-items: end;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .ganancia-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📹 Presupuesto Cámaras de Seguridad</h1>
            <p>Sistema de Cotización de Instalaciones</p>
        </div>
        
        <div class="content">
            <div class="form-section">
                <h2>➕ Agregar Producto al Presupuesto</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="producto">Seleccionar Producto:</label>
                            <select id="producto" name="producto" required>
                                <option value="">-- Seleccione un producto --</option>
                                <?php foreach ($productos_disponibles as $indice => $prod): ?>
                                    <option value="<?php echo $indice; ?>">
                                        <?php echo htmlspecialchars($prod['nombre']); ?> - 
                                        $<?php echo number_format($prod['precio'], 0, ',', '.'); ?>.-
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="cantidad">Cantidad:</label>
                            <input type="number" id="cantidad" name="cantidad" 
                                   min="1" value="1" required>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="agregar" class="btn btn-primary">
                                Agregar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="productos-list">
                <h2>📦 Productos en el Presupuesto</h2>
                
                <?php if (empty($_SESSION['productos'])): ?>
                    <div class="empty-message">
                        🛒 No hay productos agregados aún.<br>
                        Selecciona un producto y agrégalo al presupuesto.
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unit.</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['productos'] as $indice => $producto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td class="precio">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?>.-</td>
                                    <td>
                                        <form method="POST" class="cantidad-edit">
                                            <input type="hidden" name="indice" value="<?php echo $indice; ?>">
                                            <input type="number" name="nueva_cantidad" 
                                                   value="<?php echo $producto['cantidad']; ?>" 
                                                   min="1" required>
                                            <button type="submit" name="actualizar" class="btn btn-success" title="Actualizar cantidad">
                                                ✓
                                            </button>
                                        </form>
                                    </td>
                                    <td class="precio">$<?php echo number_format($producto['subtotal'], 0, ',', '.'); ?>.-</td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="indice" value="<?php echo $indice; ?>">
                                            <button type="submit" name="eliminar" class="btn btn-danger" title="Eliminar producto">
                                                ✕
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="total-section">
                        <h3>💰 Total del Presupuesto:</h3>
                        <div class="total-amount">$<?php echo number_format($total, 0, ',', '.'); ?>.-</div>
                    </div>
                    
                    <div class="actions">
                        <form method="POST">
                            <button type="submit" name="limpiar" class="btn btn-warning" 
                                    onclick="return confirm('¿Estás seguro de limpiar todos los productos del presupuesto?')">
                                🗑️ Limpiar Todo
                            </button>
                        </form>
                    </div>
                    
                    <!-- Sección de Cálculo de Ganancia -->
                    <div class="ganancia-section">
                        <h2>💵 Calcular Ganancia</h2>
                        <form method="POST">
                            <div class="ganancia-form">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label for="precio_venta">Precio de Venta al Cliente:</label>
                                    <input type="number" id="precio_venta" name="precio_venta" 
                                           step="0.01" min="0" 
                                           value="<?php echo $_SESSION['precio_venta']; ?>"
                                           placeholder="Ej: 259990" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <button type="submit" name="calcular_ganancia" class="btn btn-primary">
                                        Calcular
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <?php if ($_SESSION['precio_venta'] > 0): ?>
                            <div class="resultado-ganancia <?php echo $ganancia < 0 ? 'perdida' : ''; ?>">
                                <div class="ganancia-item">
                                    <span class="ganancia-label">Costo Total (Presupuesto):</span>
                                    <span class="ganancia-valor">$<?php echo number_format($total, 0, ',', '.'); ?>.-</span>
                                </div>
                                <div class="ganancia-item">
                                    <span class="ganancia-label">Precio de Venta:</span>
                                    <span class="ganancia-valor">$<?php echo number_format($_SESSION['precio_venta'], 0, ',', '.'); ?>.-</span>
                                </div>
                                <div class="ganancia-item <?php echo $ganancia < 0 ? 'perdida-text' : ''; ?>">
                                    <span class="ganancia-label">
                                        <?php echo $ganancia >= 0 ? '✅ Ganancia Obtenida:' : '⚠️ Pérdida:'; ?>
                                    </span>
                                    <span>
                                        $<?php echo number_format(abs($ganancia), 0, ',', '.'); ?>.-
                                        <span class="porcentaje <?php echo $ganancia < 0 ? 'negativo' : ''; ?>">
                                            <?php echo $ganancia >= 0 ? '+' : '-'; ?><?php echo number_format(abs($porcentaje_ganancia), 1); ?>%
                                        </span>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>