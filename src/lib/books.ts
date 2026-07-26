import { readFileSync } from "node:fs";
import { join } from "node:path";

export type BookYear = {
  year: string;
  count: number;
  path: string;
  source: string;
};

export const bookYears: BookYear[] = [
  { year: "2026", count: 4, path: "/livros/", source: "livros.htm" },
  { year: "2025", count: 33, path: "/livros/2025/", source: "livros/2025.htm" },
  { year: "2024", count: 17, path: "/livros/2024/", source: "livros/2024.htm" },
  { year: "2023", count: 23, path: "/livros/2023/", source: "livros/2023.htm" },
  { year: "2022", count: 34, path: "/livros/2022/", source: "livros/2022.htm" },
  { year: "2021", count: 46, path: "/livros/2021/", source: "livros/2021.htm" },
  { year: "2020", count: 28, path: "/livros/2020/", source: "livros/2020.htm" },
  { year: "2019", count: 28, path: "/livros/2019/", source: "livros/2019.htm" },
  { year: "2018", count: 36, path: "/livros/2018/", source: "livros/2018.htm" },
  { year: "2017", count: 39, path: "/livros/2017/", source: "livros/2017.htm" },
  { year: "2016", count: 3, path: "/livros/2016/", source: "livros/2016.htm" },
  { year: "2015", count: 3, path: "/livros/2015/", source: "livros/2015.htm" },
  { year: "2014", count: 14, path: "/livros/2014/", source: "livros/2014.htm" },
  { year: "2013", count: 26, path: "/livros/2013/", source: "livros/2013.htm" },
];

export const totalBooks = bookYears.reduce((total, item) => total + item.count, 0);

export function getBookYear(year: string) {
  return bookYears.find((item) => item.year === year);
}

export function getBooksMarkup(source: string) {
  const html = readFileSync(join(process.cwd(), source), "utf8");
  const start = html.indexOf('<section id="books"');
  const end = html.indexOf('<div class="row">\n      <nav class="navbar navbar-default navbar-bottom">', start);

  if (start === -1 || end === -1) {
    throw new Error(`Could not find books section in ${source}`);
  }

  return rewriteLegacyBookLinks(html.slice(start, end));
}

function rewriteLegacyBookLinks(markup: string) {
  return markup
    .replaceAll('href="/livros.htm"', 'href="/livros/"')
    .replace(/href="\/livros\/(\d{4})\.htm"/g, 'href="/livros/$1/"');
}
