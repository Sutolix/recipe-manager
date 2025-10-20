
# Plugin Gerenciador de Receitas

Um plugin WordPress para gerenciar receitas com custom post type, templates personalizados e campos extras.

## Funcionalidades

- **Custom Post Type**: Cria o tipo de post "Receita" com campos personalizados
- **Campos Personalizados**:
  - Tempo de preparo (em minutos)
  - Lista de ingredientes
- **Template de Arquivo**: Exibe as receitas em grade com imagem, título e tempo de preparo
- **Template Individual**: Mostra detalhes da receita e seção de receitas relacionadas
- **Receitas Relacionadas**: Seção "Veja também" com 3 sugestões aleatórias
- **Design Responsivo**: Templates adaptados para dispositivos móveis
- **Estilo Personalizado**: CSS moderno e atrativo

## Instalação

1. Envie a pasta `recipe-manager` para o diretório `/wp-content/plugins/` do seu WordPress
2. Ative o plugin no menu 'Plugins' do WordPress
3. O plugin criará automaticamente o tipo de post e os templates necessários

## Como Usar

### Criando Receitas

1. Acesse **Receitas** no menu administrativo do WordPress
2. Clique em **Adicionar nova receita**
3. Preencha os detalhes da receita:
   - **Título**: Nome da receita
   - **Descrição**: Descrição da receita (conteúdo principal)
   - **Imagem Destacada**: Foto da receita
   - **Tempo de Preparo**: Tempo em minutos
   - **Ingredientes**: Lista de ingredientes (um por linha)

### Visualizando Receitas

- **Página de Arquivo**: Acesse `/receitas/` para ver todas as receitas em grade
- **Página de Categoria**: Acesse `/tipo-de-receita/{slug-da-categoria}/` para ver receitas de uma categoria específica
- **Receita Individual**: Clique em uma receita para ver os detalhes completos
- **Receitas Relacionadas**: Cada página de receita exibe 3 sugestões aleatórias

## Estrutura de Arquivos

```
recipe-manager/
├── recipe-manager.php          # Arquivo principal do plugin
├── templates/
│   ├── archive-recipe.php      # Template de arquivo de receitas
│   └── single-recipe.php       # Template de receita individual
├── assets/
│   └── css/
│       └── style.css           # Estilos do plugin
└── README.md                   # Este arquivo
```

## Personalização

### Estilo
Você pode personalizar a aparência editando o CSS em `assets/css/style.css` ou adicionando CSS ao seu tema.

### Templates
Os templates podem ser personalizados editando os arquivos na pasta `templates/`.

### Hooks e Filtros
O plugin utiliza hooks padrão do WordPress e pode ser estendido usando:
- Filtro `template_include` para manipulação de templates
- Hooks de post type e meta box para ampliar funcionalidades

## Requisitos

- WordPress 5.0 ou superior
- PHP 7.4 ou superior

## Suporte

Para suporte e solicitações de customização, entre em contato com o desenvolvedor do plugin.

## Changelog

### Versão 1.0.0
- Lançamento inicial
- Custom post type para receitas
- Templates de arquivo e individual
- Funcionalidade de receitas relacionadas
- Design responsivo
- Estilo personalizado
