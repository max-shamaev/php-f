function Project_PHPSettings()
    nnoremap <C-a> :call Project_PhpDocFile()<CR>
    inoremap <C-a> <C-R>=ClassName()<CR>
    set foldenable
    set foldmethod=marker
    set foldmarker={{{,}}}
    set ts=4
    set sw=4
    set sts=4
    set et
endfunction

au Filetype php call Project_PHPSettings()

" PHP documentor tags
let g:pdv_cfg_Package    = "PHPF"
let g:pdv_cfg_Author1    = "Maxim Shamaev (maxim.shamaev@gmail.com)"
let g:pdv_cfg_License    = "Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0"
let g:pdv_cfg_Since      = "1.0.0"
let g:pdv_cfg_Link       = "https://github.com/max-shamaev/php-f"

let g:pdv_cfg_FileSee    = ""
let g:pdv_cfg_ClassSee   = ""
let g:pdv_cfg_VarSee     = ""
let g:pdv_cfg_FuncSee    = ""

let g:pdv_cfg_FileTitle  = "____file_title____"
let g:pdv_cfg_ParamComm  = "____param_comment____"

let g:pdv_cfg_vimOpts    = "// vim: set ts=4 sw=4 sts=4 et:"

let g:pdv_cfg_php4always = 1
let g:pdv_cfg_php4guess  = 0

func! ClassName()
    return substitute(substitute(substitute(expand("%:p"), '.\+test/classes/', '', 'g'), '/', '_', 'g'), '.php', '', '')
endfunc

func! CheckCS()
    let parts = split(getcwd(), '\/')
    let path = ''
    for p in parts
        let path = path . '/' . p
        let $fpath = path . '/.dev/phpcs-report.sh'
        if filereadable($fpath)
            let fp = $fpath . ' ' . expand("%:p")
            let e = system(fp)
            echo e
        endif
    endfor
endfunc

func! Project_PhpDocFile()
    " Line for the comment to begin
    let commentline = line (".") - 1

    let l:indent = matchstr(getline("."), '^\ *')

    exe "norm! " . commentline . "G$"

    " Local indent
    let l:txtBOL = g:pdv_cfg_BOL . indent

    exe l:txtBOL . g:pdv_cfg_vimOpts . g:pdv_cfg_EOL
    exe l:txtBOL . g:pdv_cfg_EOL


    exe l:txtBOL . g:pdv_cfg_CommentHead . g:pdv_cfg_EOL

    exe l:txtBOL . g:pdv_cfg_Commentn . "PHP version 5.3.0" . g:pdv_cfg_EOL
    exe l:txtBOL . g:pdv_cfg_Commentn . g:pdv_cfg_EOL
    exe l:txtBOL . g:pdv_cfg_Commentn . "@author  " . g:pdv_cfg_Author1 g:pdv_cfg_EOL
    exe l:txtBOL . g:pdv_cfg_Commentn . "@license " . g:pdv_cfg_License . g:pdv_cfg_EOL
    exe l:txtBOL . g:pdv_cfg_Commentn . "@version " . g:pdv_cfg_Version . g:pdv_cfg_EOL
    exe l:txtBOL . g:pdv_cfg_Commentn . "@link    " . g:pdv_cfg_Link . g:pdv_cfg_EOL
    exe l:txtBOL . g:pdv_cfg_Commentn . "@since   " . g:pdv_cfg_Since . g:pdv_cfg_EOL

    " Close the comment block.
    exe l:txtBOL . g:pdv_cfg_CommentTail . g:pdv_cfg_EOL

endfunc

:nmap <F10> :call CheckCS()<CR>
