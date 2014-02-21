" .vimrc
" Jaimie Murdock
" June 6, 2012 

set nocompatible        "use Vim defaults
set noerrorbells        "don't make noise

"formatting
set autoindent          "indent wrt current line
set smartindent         "indent using C conventions

set expandtab           "expand tabs to spaces
set softtabstop=4       "treat 4 spaces as a tab
set tabstop=4           "tabs are four spaces
set shiftwidth=4
set bs=indent,eol,start "allow backspacing over everything in insert mode
set nowrap

"display settings
set background=dark     "set background color to dark 
set ruler               "display position
set laststatus=2        "always show the status line
set showcmd             "shows command in status line
set showmatch           "show matching brackets
set ttyfast             "smoother scrolling
syntax on               "syntax highlighting
let html_use_css = 1    "turn on css for TOhtml 
"set cursorline         "highlight current line
set lcs=tab:\ \ ,extends:>,precedes:< " Show < > when line is offscreen

"set textwidth to 80 because people are lame
set textwidth=80

"misc overrides of default color highlighting
hi Comment ctermfg=Green
hi String ctermfg=LightBlue
hi LineNr ctermfg=grey ctermbg=darkgrey
hi StatusLine cterm=bold,underline
hi StatusLineNC cterm=underline ctermfg=darkgrey

"linenumbers
"set number              "turn on line numbers
set numberwidth=6       "good for up to 9999 lines

" Syntax highlighting for sql and make files
filetype plugin on
au BufNewFile,BufRead *.make set filetype=make
au BufNewFile,BufRead *.sql set filetype=sql
au BufNewFile,BufRead *.rst set filetype=rest

"set scheme code to work with 2 spaces only
au FileType scheme setl sw=2 ts=2 sts=2 si ai lisp
au FileType javascript setl sw=2 ts=2 sts=2 si ai
au FileType html setl sw=2 ts=2 sts=2 tw=0 si ai
au FileType latex setl sw=2 ts=2 sts=2 tw=0 si ai

"set javascript code to work with 2 spaces only

"folding
set foldenable
set foldmarker={,}
set foldmethod=marker
set foldlevel=100

"auto-complete with smart-tabbing
set ofu=syntaxcomplete#Complete
function! CleverTab()
  if pumvisible()
    return "\<C-N>"
  endif
  if strpart( getline('.'), 0, col('.')-1 ) =~ '^\s*$'
    return "\<Tab>"
  elseif exists('&omnifunc') && &omnifunc != ''
    return "\<C-X>\<C-O>"
  else
    return "\<C-N>"
  endif
endfunction
inoremap <S-Tab> <C-R>=CleverTab()<CR>
