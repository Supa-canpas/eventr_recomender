import requests
from bs4 import BeautifulSoup

def getInfo(url):
    # url = 'https://collabo-cafe.com/events/collabo/shingeki-gigo-cafe-ikebukuro2024/'
    res = requests.get(url)
    soup = BeautifulSoup(res.text, 'html.parser')
    news = soup.select_one('#main > article > section.entry-content > div.table__container > table')
    records = news.find_all('tr')
    info = []
    count = 0
    for record in records:
        if count == 0:
            pass
        elif count == 3:
            buf = record.find('a')['href']
            info.append(buf)
        else:
            buf = record.find('td').text
            info.append(buf)
        count += 1
    return info

if __name__ == '__main__':
    pass